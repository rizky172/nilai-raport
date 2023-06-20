<?php
namespace App\Libs\Mails;

use App\Libs\Helpers\FullPath;
use App\Libs\Mails\Mail;
use App\Libs\Repository\Config;
use App\Libs\Repository\GuestQuotationOnline;

use App\Models\Quotation as Model;

class QuotationOnlineMail extends Mail
{
    private $model;
    private $email;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getData()
    {
        $messages = null;

        $data = Config::get('quotaion_online_email');

        $noQuotation = $this->model->ref_no;
        $companyName = $this->model->company_name;
        $pic = $this->model->company_pic;

        $replace_array = [
            '{no_quotation}' => $noQuotation,
            '{company_name}' => $companyName,
            '{pic}' => $pic,
            "\n" => '<br>'
        ];

        $messages = str_replace(array_keys($replace_array),array_values($replace_array), $data);

        return [
            'messages' => $messages,
        ];
    }

    // public function getAttachment()
    // {
    //     $url = FullPath::tmp('');

    //     return $url;
    // }

    public function getView()
    {
        return 'email.quotation-online';
    }

    public function getSubject()
    {
        $title = Config::get('quotaion_online_title');

        return $title;
    }

    public function sendMail()
    {
        parent::sendMail();
    }
}
