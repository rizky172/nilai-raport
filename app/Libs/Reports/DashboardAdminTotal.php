<?php

namespace App\Libs\Reports;

use App\WevelopeLibs\Helper\DateFormat;

use App\Libs\Repository\Finder\ReportDashboardAdminFinder;
use App\Libs\Repository\Config;

use App\Category;

class DashboardAdminTotal extends ReportDashboardAdminFinder
{
    public function getTotal()
    {
        $category = Category::where('group_by', 'currency')->get();
        $companyWalletId = Config::get('profit_sharing_wallet');

        $currencyIdr = $category->firstWhere('name', 'idr');
        $currencyBusd = $category->firstWhere('name', 'busd');

        $this->setPage('all');
        $temp = $this->get();

        $profitSharingAffiliate = $temp->where('currency_id', $currencyIdr->id)
                                ->where('category_name', 'profit_sharing_affiliate')
                                ->where('wallet_id', '!=', $companyWalletId)
                                ->sum('amount');

        $withdrawIdr = $temp->where('currency_id', $currencyIdr->id)
                                ->whereIn('category_name', ['withdraw', 'withdraw_fee'])
                                ->where('wallet_id', '!=', $companyWalletId)
                                ->sum('amount');

        $profitSharingGasFee = $temp->where('currency_id', $currencyBusd->id)
                                    ->where('category_name', 'profit_sharing')
                                    ->where('wallet_id', '!=', $companyWalletId)
                                    ->sum('amount');

        $withdrawBusd = $temp->where('currency_id', $currencyBusd->id)
                                ->whereIn('category_name', ['withdraw', 'withdraw_fee'])
                                ->where('wallet_id', '!=', $companyWalletId)
                                ->sum('amount');

        $totalDepositFee = $temp->where('currency_id', $currencyBusd->id)
                                ->where('category_name', 'deposit_fee')
                                ->where('wallet_id', '!=', $companyWalletId)
                                ->sum('amount');

        $totalWithdrawBusdFee = $temp->where('currency_id', $currencyBusd->id)
                                ->where('category_name', 'withdraw_fee')
                                ->where('wallet_id', '!=', $companyWalletId)
                                ->sum('amount');

        $totalWithdrawIdrdFee = $temp->where('currency_id', $currencyIdr->id)
                                ->where('category_name', 'withdraw_fee')
                                ->where('wallet_id', '!=', $companyWalletId)
                                ->sum('amount');

        $baseUrl = env('APP_URL');

        $categoryTrx = Category::where('group_by', 'trx')->get();

        $trxProfitSharingAffiliate = $categoryTrx->firstWhere('name', 'profit_sharing_affiliate');
        $trxProfitSharing = $categoryTrx->firstWhere('name', 'profit_sharing');
        $trxDepositFee = $categoryTrx->firstWhere('name', 'deposit_fee');
        $trxWithdraw = $categoryTrx->firstWhere('name', 'withdraw');
        $trxWithdrawFee = $categoryTrx->firstWhere('name', 'withdraw_fee');

        $categoryWithdraw = sprintf('%s,%s', $trxWithdraw->id, $trxWithdrawFee->id);
        $categoryAvailableProfitSharingAffiliate = sprintf('%s,%s', $trxProfitSharingAffiliate->id, $categoryWithdraw);
        $categoryAvailableProfitSharing = sprintf('%s,%s', $trxProfitSharing->id, $categoryWithdraw);

        $dateFromParam = '';
        if($this->getDateStart()){
            $dateFrom = str_replace('/', '-',DateFormat::shortDate($this->getDateStart()));
            $dateFromParam = sprintf('&date_from=%s', $dateFrom);
        }

        $dateToParam = '';
        if($this->getDateFinish()){
            $dateTo = str_replace('/', '-',DateFormat::shortDate($this->getDateFinish()));
            $dateToParam = sprintf('&date_to=%s', $dateTo);
        }

        $dateRange = sprintf('%s%s', $dateFromParam, $dateToParam);

        $data = [
            [
                'title' => 'Affiliate Member - Total All Time',
                'total' => $profitSharingAffiliate,
                'unit' => $currencyIdr->label,
                'unit_name' => $currencyIdr->name,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyIdr->id, $trxProfitSharingAffiliate->id, $dateRange)
            ],
            [
                'title' => 'Affiliate Member - Available',
                'total' => $withdrawIdr + $profitSharingAffiliate,
                'unit' => $currencyIdr->label,
                'unit_name' => $currencyIdr->name,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyIdr->id, $categoryAvailableProfitSharingAffiliate, $dateRange)
            ],
            [
                'title' => 'Affiliate Member - Withdraw',
                'total' => abs($withdrawIdr),
                'unit' => $currencyIdr->label,
                'unit_name' => $currencyIdr->name,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyIdr->id, $categoryWithdraw, $dateRange)
            ],
            [
                'title' => 'Profit Sharing Gas Fee - Total All Time',
                'total' => $profitSharingGasFee,
                'unit' => $currencyBusd->label,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyBusd->id, $trxProfitSharing->id, $dateRange)
            ],
            [
                'title' => 'Profit Sharing Gas Fee - Available',
                'total' => $withdrawBusd + $profitSharingGasFee,
                'unit' => $currencyBusd->label,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyBusd->id, $categoryAvailableProfitSharing, $dateRange)
            ],
            [
                'title' => 'Profit Sharing Gas Fee - Withdraw',
                'total' => abs($withdrawBusd),
                'unit' => $currencyBusd->label,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyBusd->id, $categoryWithdraw, $dateRange)
            ],
            [
                'title' => 'Total Deposit Fee',
                'total' => abs($totalDepositFee),
                'unit' => $currencyBusd->label,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyBusd->id, $trxDepositFee->id, $dateRange)
            ],
            [
                'title' => 'Total Withdraw Fee',
                'total' => abs($totalWithdrawBusdFee),
                'unit' => $currencyBusd->label,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyBusd->id, $trxWithdrawFee->id, $dateRange)
            ],
            [
                'title' => 'Total Withdraw Fee',
                'total' => abs($totalWithdrawIdrdFee),
                'unit' => $currencyIdr->label,
                'unit_name' => $currencyIdr->name,
                'link' => sprintf('%s/#/report/history-transaction?currency_id=%s&wallet=member&category_id=%s%s', $baseUrl, $currencyIdr->id, $trxWithdrawFee->id, $dateRange)
            ]

        ];

        return $data;
    }

    public static function createFromReportDashboardAdminFinder(ReportDashboardAdminFinder $finder)
    {
        $data = new self();

        $param = $finder->getDateStart();
        if (!empty($param)) {
            $data->setDateStart($param);
        }

        $param = $finder->getDateFinish();
        if (!empty($param)) {
            $data->setDateFinish($param);
        }

        return $data;
    }
}