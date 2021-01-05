<?php
/**
 * Created by Furkan Bostancı.
 * User: steetstyle
 * Date: 05/01/2021
 * Time: 20:25 
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

class DashboardController extends AdminController
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

    public function getInformation(){
        return $this->sendResponseOk([
            'users_count' => $this->getUsersCount(),
            'branches_count' => $this->getBranchesCount(),
            'cards_count' => $this->getCardsCount()
        ], "Get informations ok.");
    }

    public function getUsersCount(){
        return $this->userRepository->listUsers([null])->count();
    }

    public function getCardsCount(){
        return $this->cardRepository->index(null)->count();
    }

    public function getBranchesCount(){
        return $this->branchRepository->index([null])->count();
    }
}