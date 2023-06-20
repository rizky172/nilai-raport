<?php

namespace App\Libs\Libs;

use DB;
use Illuminate\Support\Facades\Validator;
use App\Libs\Libs\DepositCalculator;
use App\Libs\Libs\AffiliateCalculator;

use App\Libs\Repository\Config;
use App\Libs\Repository\AbstractRepository;
use App\Libs\Repository\Trx as TrxRepo;


use App\Trx;
use App\Order;
use App\Category;
use App\Deposit;
use App\Person;
use App\Invoice;
use App\Item;
use App\Meta;

class TransactionGenerator
{
    /*
     * @param int id you want to check
     * @param id as deposit_id
     */
    private static function validateConfigProfitSharing($profitSharing, $profitBersih, $sponsor)
    {
        $fields = [
            'profit_sharing' => $profitSharing,
            'profit_bersih' => $profitBersih,
            'sponsor' => $sponsor,
        ];

        $rules = [
            'profit_sharing' => 'required',
            'profit_bersih' => 'required',
            'sponsor' => 'required',
        ];

        $messages = [
            'profit_sharing.required' => 'Transaksi > Profit Sharing pada halaman pengaturan belum diisi.',
            'profit_bersih.required' => 'Transaksi > Profit Sharing pada Produk Paket belum diisi.',
            'sponsor.required' => 'Transaksi > Sponsor pada halaman pengaturan belum diisi.',
        ];

        $validator = Validator::make($fields, $rules, $messages);
        AbstractRepository::validOrThrow($validator);
    }

    public static function deposit($id, $personWalletId, $companyWalletId, $amount, $fee, $created)
    {
        $userMessages = 'Deposit berhasil';
        $feeMessages = 'Potongan fee deposit';
        $incomeMessages = 'Fee deposit';

        $category = Category::whereIn('group_by', ['trx', 'currency'])->get();

        $trxCategory = $category->filter(function($x) {
            return $x->group_by == 'trx';
        });

        $currencyCategory = $category->filter(function($x) {
            return $x->group_by == 'currency';
        });

        $categoryDeposit = $trxCategory->firstWhere('name', 'deposit')->id;
        $categoryDepositFee = $trxCategory->firstWhere('name', 'deposit_fee')->id;
        $currencyId = $currencyCategory->firstWhere('name', 'busd')->id;

        $tableName = null;
        $fkId = null;
        $trxId = null;

        if ($id) {
            $deposit = Deposit::find($id);
            $tableName = $deposit->getTable();
            $personWalletId = $deposit->person->wallet_id;
            $fkId = $id;
        }

        //deposit member
        $trxDepositId = self::generateTrx($categoryDeposit, $trxId, $currencyId, $personWalletId, $tableName, $fkId, $amount, $userMessages, $created);

        $realFee = $fee;
        if ($amount < $realFee) {
            $realFee = $amount;
        }

        //deposit fee member
        $trxPotonganId = self::generateTrx($categoryDepositFee, $trxDepositId, $currencyId, $personWalletId, $tableName, $fkId, -$realFee, $feeMessages, $created);

        //deposit fee company
        self::generateTrx($categoryDepositFee, $trxPotonganId, $currencyId, $companyWalletId, $tableName, $fkId, $realFee, $incomeMessages, $created);
    }

    public static function withdraw($id, $personWalletId, $companyWalletId, $currencyId, $amount, $fee, $created)
    {
        $userMessages = 'Withdraw berhasil';
        $feeMessages = 'Potongan fee withdraw';
        $incomeMessages = 'Fee withdraw';

        $category = Category::whereIn('group_by', ['trx', 'currency'])->get();

        $trxCategory = $category->filter(function($x) {
            return $x->group_by == 'trx';
        });

        $categoryWithdraw = $trxCategory->firstWhere('name', 'withdraw')->id;
        $categoryWithdrawFee = $trxCategory->firstWhere('name', 'withdraw_fee')->id;

        $tableName = null;
        $fkId = null;
        $trxId = null;

        if ($id) {
            $withdraw = Deposit::find($id);
            $tableName = $withdraw->getTable();
            $personWalletId = $withdraw->person->wallet_id;
            $fkId = $id;
        }

        $amountWithdraw = $amount - $fee;

        //withdraw member
        $trxWithdrawId = self::generateTrx($categoryWithdraw, $trxId, $currencyId, $personWalletId, $tableName, $fkId, -$amountWithdraw, $userMessages, $created);

        $realFee = $fee;
        if ($amount < $realFee) {
            $realFee = $amount;
        }

        //withdraw fee member
        $trxPotonganId = self::generateTrx($categoryWithdrawFee, $trxWithdrawId, $currencyId, $personWalletId, $tableName, $fkId, -$realFee, $feeMessages, $created);

        //withdraw fee company
        self::generateTrx($categoryWithdrawFee, $trxPotonganId, $currencyId, $companyWalletId, $tableName, $fkId, $realFee, $incomeMessages, $created);
    }

    public static function profitSharingTrading($id, $person, $companyWalletId, $uplineWalletId, $amount, $created)
    {
        $personWalletId = $person->wallet_id;
        $personTypeId = $person->type_id;

        $category = Category::whereIn('group_by', ['trx', 'currency'])->get();

        $trxCategory = $category->filter(function($x) {
            return $x->group_by == 'trx';
        });

        $currencyCategory = $category->filter(function($x) {
            return $x->group_by == 'currency';
        });

        $profitSharingProduk = Category::select('meta_item.*')
                                        ->join('meta as meta_category', function($query) {
                                            $query->on('meta_category.table_name', '=', DB::raw("'category'"));
                                            $query->on('meta_category.key', '=', DB::raw("'item_id'"));
                                            $query->on('meta_category.fk_id', '=', "category.id");
                                        })
                                        ->join('item', 'item.id', '=', 'meta_category.value')
                                        ->join('meta as meta_item', function($query) {
                                            $query->on('meta_item.table_name', '=', DB::raw("'item'"));
                                            $query->on('meta_item.key', '=', DB::raw("'profit_sharing'"));
                                            $query->on('meta_item.fk_id', '=', "item.id");
                                        })
                                        ->where('category.group_by', 'customer_type')
                                        ->where('category.id', $personTypeId)
                                        ->first()
                                        ->value ?? null;

        $configProfitSharing = Config::get('profit_sharing');
        $configSponsor = Config::get('sponsor');

        self::validateConfigProfitSharing($configProfitSharing, $profitSharingProduk, $configSponsor);

        $profitGasFeeMessages = sprintf("Gas Fee sebesar %s%% dari nominal %s %s", $profitSharingProduk, $amount, $currencyCategory->firstWhere('name', 'busd')->label);
        $profitSharingMessages = 'Profit sharing dari Gas Fee';

        $categoryGasFeeId = $trxCategory->firstWhere('name', 'gas_fee')->id;
        $categoryProfitSharingId = $trxCategory->firstWhere('name', 'profit_sharing')->id;
        $currencyId = $currencyCategory->firstWhere('name', 'busd')->id;

        $tableName = null;
        $fkId = null;
        $trxId = null;

        if ($id) {
            $order = Order::find($id);
            $tableName = $order->getTable();
            $personWalletId = $order->tradePlan->member->wallet_id;
            $fkId = $id;
        }

        $gasFee = $amount * ($profitSharingProduk / 100);
        $trxProfitBersihId = self::generateTrx($categoryGasFeeId, $trxId, $currencyId, $personWalletId, $tableName, $fkId, -$gasFee, $profitGasFeeMessages, $created);

        $unityPump = $gasFee * ($configProfitSharing / 100);
        self::generateTrx($categoryProfitSharingId, $trxProfitBersihId, $currencyId, $companyWalletId, $tableName, $fkId, $unityPump, $profitSharingMessages, $created);

        $sponsor = $gasFee * ($configSponsor / 100);
        self::generateTrx($categoryProfitSharingId, $trxProfitBersihId, $currencyId, $uplineWalletId, $tableName, $fkId, $sponsor, $profitSharingMessages, $created);
    }

    public static function profitSharingAffiliate($id, $personId, $companyWalletId, $produkMembershipId, $created)
    {
        $sponsorMessages = 'Sponsor';

        $category = Category::whereIn('group_by', ['trx', 'currency'])->get();
        $person = Person::find($personId);

        $trxCategory = $category->filter(function($x) {
            return $x->group_by == 'trx';
        });

        $currencyCategory = $category->filter(function($x) {
            return $x->group_by == 'currency';
        });

        $categoryId = $trxCategory->firstWhere('name', 'profit_sharing_affiliate')->id;
        $currencyIdr = $currencyCategory->firstWhere('name', 'idr')->id;
        $currencyRewardPoint = $currencyCategory->firstWhere('name', 'reward_point')->id;

        $tableName = null;
        $fkId = null;
        $items = Item::find($produkMembershipId);
        $companyMessage = $items->name;

        if ($id) {
            $invoice = Invoice::find($id);
            $tableName = $invoice->getTable();
            $fkId = $id;
            $items = $invoice->details->firstWhere('item_id', $produkMembershipId);
            $companyMessage = $items->item->name;
        }

        $calcAffiliate = self::generateAffiliate($person, $produkMembershipId);

        $getSponsorIdr = $calcAffiliate['get_sponsor_idr'];
        $getSponsorPoint = $calcAffiliate['get_sponsor_point'];
        $getCompanyIdr = $calcAffiliate['get_company_idr'];

        $sponsorWalletId = $person->upline->wallet_id;

        if($getSponsorIdr > 0){
            // reward idr
            self::generateTrx($categoryId, null, $currencyIdr, $sponsorWalletId, $tableName, $fkId, $getSponsorIdr, $sponsorMessages, $created);
        }

        if($getSponsorPoint > 0){
            // reward point
            self::generateTrx($categoryId, null, $currencyRewardPoint, $sponsorWalletId, $tableName, $fkId, $getSponsorPoint, $sponsorMessages, $created);
        }

        if($getCompanyIdr > 0){
            // income company
            self::generateTrx($categoryId, null, $currencyIdr, $companyWalletId, $tableName, $fkId, $getCompanyIdr, $companyMessage, $created);
        }

    }

    public static function incomeCompany($id, $companyWalletId, $income, $notes, $created)
    {
        $category = Category::whereIn('group_by', ['trx', 'currency'])->get();

        $trxCategory = $category->filter(function($x) {
            return $x->group_by == 'trx';
        });

        $currencyCategory = $category->filter(function($x) {
            return $x->group_by == 'currency';
        });

        $categoryId = $trxCategory->firstWhere('name', 'income')->id;
        $currencyIdr = $currencyCategory->firstWhere('name', 'idr')->id;

        $invoice = Invoice::find($id);
        $tableName = $invoice->getTable();
        $fkId = $id;

        // income company
        self::generateTrx($categoryId, null, $currencyIdr, $companyWalletId, $tableName, $fkId, $income, $notes, $created);
    }

    private static function generateAffiliate($person, $produkId)
    {
        $memberTypeId = $person->type_id;
        $sponsorTypeId = $person->upline->type_id;

        $meta = Meta::select('meta.*')
                    ->join('category', 'category.id', '=', 'meta.fk_id')
                    ->where('category.group_by', 'customer_type')
                    ->get();

        $metaItemId = $meta->filter(function($x) {
            return $x->key == 'item_id';
        });

        $metaLevel = $meta->filter(function($x) {
            return $x->key == 'level';
        });

        $metaBonusItem = Meta::select('meta.*', 'item.price')
                    ->join('item', 'item.id', '=', 'meta.fk_id')
                    ->where('meta.table_name', 'item')
                    ->get();

        $metaBonusIdr = $metaBonusItem->filter(function($x) {
            return $x->key == 'bonus_idr';
        });

        $metaBonusPoint = $metaBonusItem->filter(function($x) {
            return $x->key == 'bonus_point';
        });

        $metaProfitSharing = $metaBonusItem->filter(function($x) {
            return $x->key == 'profit_sharing';
        });

        // package member
        $metaMemberItem = $metaItemId->firstWhere('fk_id', $memberTypeId)->value;
        $metaMemberLevel = $metaLevel->firstWhere('fk_id', $memberTypeId)->value;

        $priceProdukMember = $metaBonusIdr->firstWhere('fk_id', $metaMemberItem)->price;
        $bonusIdrProdukMember = $metaBonusIdr->firstWhere('fk_id', $metaMemberItem)->value;
        $bonusPointProdukMember = $metaBonusPoint->firstWhere('fk_id', $metaMemberItem)->value;
        $bonusProfitSharingProdukMember = $metaProfitSharing->firstWhere('fk_id', $metaMemberItem)->value;

        // package buy produk
        $metaProduk = $metaItemId->firstWhere('value', $produkId);
        $metaProdukLevel = $metaLevel->firstWhere('fk_id', $metaProduk->fk_id)->value;

        $priceBuyProduk = $metaBonusIdr->firstWhere('fk_id', $metaProduk->value)->price;
        $bonusIdrBuyProduk = $metaBonusIdr->firstWhere('fk_id', $metaProduk->value)->value;
        $bonusPointBuyProduk = $metaBonusPoint->firstWhere('fk_id', $metaProduk->value)->value;
        $bonusProfitSharingBuyProduk = $metaProfitSharing->firstWhere('fk_id', $metaProduk->value)->value;

        // // package sponsor
        $metaSponsorItem = $metaItemId->firstWhere('fk_id', $sponsorTypeId)->value;
        $metaSponsorLevel = $metaLevel->firstWhere('fk_id', $sponsorTypeId)->value;

        $priceProdukSponsor = $metaBonusIdr->firstWhere('fk_id', $metaSponsorItem)->price;
        $bonusIdrProdukSponsor = $metaBonusIdr->firstWhere('fk_id', $metaSponsorItem)->value;
        $bonusPointProdukSponsosr = $metaBonusPoint->firstWhere('fk_id', $metaSponsorItem)->value;
        $bonusProfitSharingProdukSponsor = $metaProfitSharing->firstWhere('fk_id', $metaSponsorItem)->value;

        $packageMember = [
            'price' => $priceProdukMember,
            'bonus_idr' => $bonusIdrProdukMember,
            'bonus_point' => $bonusPointProdukMember,
            'profit_sharing' => $bonusProfitSharingProdukMember,
            'level' => $metaMemberLevel
        ];

        $packageBuyProduk = [
            'price' => $priceBuyProduk,
            'bonus_idr' => $bonusIdrBuyProduk,
            'bonus_point' => $bonusPointBuyProduk,
            'profit_sharing' => $bonusProfitSharingBuyProduk,
            'level' => $metaProdukLevel
        ];

        $packageSponsor = [
            'price' => $priceProdukSponsor,
            'bonus_idr' => $bonusIdrProdukSponsor,
            'bonus_point' => $bonusPointProdukSponsosr,
            'profit_sharing' => $bonusProfitSharingProdukSponsor,
            'level' => $metaSponsorLevel
        ];

        $pointToIdr = Config::get('point_to_idr');

        $from = $packageMember;
        $to = $packageBuyProduk;
        $sponsor = $packageSponsor;

        $calc = new AffiliateCalculator();
        $calc->setOldPackage($from);
        $calc->setNewPackage($to);
        $calc->setSponsorPackage($sponsor);
        $calc->setPointToIdr($pointToIdr);

        $response = [
          'get_sponsor_idr' => $calc->getSponsorIdr(),
          'get_sponsor_point' => $calc->getSponsorPoint(),
          'get_company_idr' => $calc->getCompanyIdr(),
        ];

        return $response;
    }

    private static function generateTrx($categoryId, $trxId, $currencyId, $walletId, $tableName, $fkId, $amount, $notes, $created)
    {
        $row = new Trx;
        $row->trx_category_id = $categoryId;
        $row->trx_id = $trxId;
        $row->currency_id = $currencyId;
        $row->wallet_id = $walletId;
        $row->table_name = $tableName;
        $row->fk_id = $fkId;
        $row->amount = $amount;
        $row->notes = $notes;
        $row->created = new \DateTime($created);

        $repo = new TrxRepo($row);
        $repo->save();

        return $row->id;
    }
}
