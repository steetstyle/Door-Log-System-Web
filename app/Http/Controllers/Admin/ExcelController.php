<?php
/**
 * Created by Furkan Bostancı.
 * User: steetstyle
 * Date: 14/01/2021
 * Time: 13:25 
 */

namespace App\Http\Controllers\Admin;


use App\Components\Core\Menu\MenuItem;
use App\Components\Core\Menu\MenuManager;
use App\Components\User\Models\User;

use App\Components\User\Repositories\UserRepository;
use App\Components\Card\Repositories\CardRepository;
use App\Components\User\Repositories\DayOffRepository;
use App\Components\Card\Repositories\CardLoginRepository;
use App\Components\Branch\Repositories\BranchRepository;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CollectionExport;
use App\Exports\ArrayExport;

class ExcelController extends AdminController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
  
    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * @var DayOffRepository
     */
    private $dayoffRepository;
   
    /**
     * @var CardLoginRepository
     */
    private $cardLoginRepository;

    /**
     * @var BranchRepository
     */
    private $branchRepository;

    /**
     * DashboardController constructor.
     * @param UserRepository $userRepository
     * @param CardRepository $cardRepository
     * @param DayOffRepository $dayoffRepository
     * @param CardLoginRepository $cardLoginRepository
     * @param BranchRepository $branchRepository

     */
    public function __construct(
        UserRepository $userRepository,
        CardRepository $cardRepository,
        DayOffRepository $dayoffRepository,
        CardLoginRepository $cardLoginRepository,
        BranchRepository $branchRepository
        )
    {
        $this->userRepository = $userRepository;       
        $this->cardRepository = $cardRepository;
        $this->dayoffRepository = $dayoffRepository;
        $this->cardLoginRepository = $cardLoginRepository;
        $this->branchRepository = $branchRepository;
    }



    public function exportCardLogs(){

        function array_flatten($array = null, $prevName = '') {
            $result = array();
        
            if (!is_array($array)) {
                $array = func_get_args();
            }
        
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $result = array_merge($result, array_flatten($value, $key));
                } else {
                    if($value == null){
                        if($key == 'user'){
                            $result =  array_merge(
                                $result,
                                array(
                                    "user_name" => "KAYITLI DEĞİL",
                                    "user_email" => "KAYITLI DEĞİL",
                                    "user_last_login" => "KAYITLI DEĞİL",
                                    "user_active" => "KAYITLI DEĞİL"
                                )
                            );
                        }
                        else if($key == "branch"){
                            $result =  array_merge(
                                $result,
                                array(
                                    "branch_name" => "KAYITLI DEĞİL",
                                    "branch_created_at" => "KAYITLI DEĞİL",
                                    "branch_updated_at" => "KAYITLI DEĞİL",
                                    "branch_tag" => "KAYITLI DEĞİL"
                                )
                            );
                        }
                        else if($key == "card"){
                            $result =  array_merge(
                                $result,
                                array(
                                    "card_id" =>  "KAYITLI DEĞİL",
                                    "card_key" =>  "KAYITLI DEĞİL",
                                    "card_user_id" =>  "KAYITLI DEĞİL",
                                    "card_branch_id" => "KAYITLI DEĞİL",
                                    "card_created_at" =>  "KAYITLI DEĞİL",
                                    "card_updated_at" =>  "KAYITLI DEĞİL"
                                )
                            );
                        }
                        else {
                            $result = array_merge($result, array(( !empty($prevName) ? $prevName.'_' : '').$key => $value));
                        }

                    }
                    else{
                        $result = array_merge($result, array(( !empty($prevName) ? $prevName.'_' : '').$key => $value));
                    }
                }
            }
        
            return $result;
        }

        $ldate = date('Y-m-d H:i:s');
        $data = $this->cardLoginRepository->index(
            request()->all(),
        )->toArray();


        $logList = array();

        foreach ($data as $item){
            $todo =array_flatten($item);
            $tada = ksort($todo,0);
            array_push($logList, $todo);
        }

        $headings = [
            '#LOG ID',
            'KART KEY',
            'ŞUBE ID',
            'GİRİŞ YAPMASI GEREKEN SAAT',
            'MAKSİMUM GEÇ KALACABİLECEĞİ SAAT',
            'ÇIKIŞ SAATİ',
            'KART GİRİŞ TARİHİ DEĞİŞTİRİLMEMİŞ',
            'DÜZENLENMİŞ KART GİRİŞ TARİHİ',
            'KART GİRİŞ TARİHİ',
            '#KART ID',
            'KART KEY',
            '#KART KULLANICI ID',
            'KART KAYITLI ŞUBE ID',
            'KARTIN OLUŞTURULMA TARİHİ',
            'KARTIN EN SON GÜNCELLENME TARİHİ',
            'ŞUBE KEYİ',
            'ŞUBE OLUŞTURULMA TARİHİ',
            'ŞUBE EN SON GÜNCELLENME TARİHİ',
            'ŞUBE İSMİ',
            'KULLANICI ADI',
            'KULLANICI EMAİL',
            'KULLANICI PANELE SON GİRİŞ TARİHİ',
            'KULLANICI AKTİF Mİ?',
        ];

        $styles = [
            'B' => [
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => 
                        array(
                        'rgb' => 'CCCCCC'
                        )
                ),
            ],
            'D' => [
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => 
                        array(
                        'rgb' => 'CCCCCC'
                        )
                ),
            ],
            'F' => [
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => 
                        array(
                        'rgb' => 'CCCCCC'
                        )
                ),
            ],
            'H' => [
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => 
                        array(
                        'rgb' => 'CCCCCC'
                        )
                ),
            ],
            'K' => [
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => 
                        array(
                        'rgb' => 'CCCCCC'
                        )
                ),
            ],
            'L' => [
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => 
                        array(
                        'rgb' => 'CCCCCC'
                        )
                ), 
            ],
            'N' => [
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => 
                        array(
                        'rgb' => 'CCCCCC'
                        )
                ), 
            ],
            1    => [
                'font' =>  
                    [
                        'bold' => true,
                       
                        'color' => array('argb'=>'FFFFFF')
                    ],
                'fill' => array(
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => array('argb' => '2c3e50')
                ),
            ],
        ];
        
        $template = new ArrayExport($logList, $headings, $styles);

        return Excel::download($template, $ldate.'-log.xlsx');
    }


}