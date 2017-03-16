<?php

namespace App\Providers;

use App\Category;
use App\MaintenanceWarning;
use App\PageText;
use Illuminate\Support\ServiceProvider;
use File;
use Session;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //С помощью View Composer мы передаем переменную в хежер
        $this->composeSmallHeader();
        $this->composeBigHeader();
        $this->composeMaintenanceMessage();
        $this->composeAdminHeader();
        $this->composeMyOrders();
        $this->composeMyJobs();
        $this->composePageText();
        $this->composeMessages();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /**
     * Compose the small header
     */
    public function composeSmallHeader()
    {
        view()->composer('partials.small-header', function ($view) {
            $view->with('userData', \Auth::user());
            $view->with('categories', Category::all()->prepend(new Category(['title' => 'All Categories'])));
        });
    }

    /**
     * Random image getting method
     */
    private function getRandomBgImage()
    {
        $imagesArray = File::files('../public/images/homepage-bg');
        if(count($imagesArray) == 0){
            return '../img/base.png';
        }
        elseif(count($imagesArray) == 1){
            return '../images/homepage-bg/'.pathinfo($imagesArray[0])['basename'];
        }
        else{
            $imageName = pathinfo($imagesArray[rand(0, count($imagesArray) - 1)])['basename'];
            if($imageName == Session::get('previousImageName')){
                return $this->getRandomBgImage();
            }
            else{
                Session::put('previousImageName', $imageName);
                return '../images/homepage-bg/'.$imageName;
            }
        }
    }

    /**
     * Compose the big header
     */
    public function composeBigHeader()
    {
        view()->composer('partials.big-header', function ($view) {

            $imageName = $this->getRandomBgImage();
            $view->with('userData', \Auth::user());
            $view->with('categories', Category::all()->prepend(new Category(['title' => 'All Categories'])));
            $view->with('bgImagePath', $imageName);
        });
    }

    /**
    * Compose the maintenance mode message
    */
    public function composeMaintenanceMessage()
    {
        view()->composer('partials.maintenance-message', function ($view) {
            $maintenanceWarning = MaintenanceWarning::first();
            $view->with('maintenanceWarning', $maintenanceWarning);
        });
    }


    public function composeAdminHeader()
    {
        view()->composer('layouts.admin', function ($view) {
            $view->with('userData', \Auth::user());
            $view->with('categories', Category::all());
        });
    }

    //Push array with page texts to all pages
    public function composePageText()
    {

        view()->composer('pages.main', function ($view) {
            $pageTexts = array();
            $texts = PageText::all();
            foreach ($texts as $text){
                $pageTexts[$text->id] = $text->value;
            }
            $view->with('pageTexts', $pageTexts);
        });
        view()->composer('pages.jobs.job', function ($view) {
            $pageTexts = array();
            $texts = PageText::all();
            foreach ($texts as $text){
                $pageTexts[$text->id] = $text->value;
            }
            $view->with('pageTexts', $pageTexts);
        });

        view()->composer('layouts.main', function ($view) {
            $pageTexts = array();
            $texts = PageText::all();
            foreach ($texts as $text){
                $pageTexts[$text->id] = $text->value;
            }
            $view->with('pageTexts', $pageTexts);
        });
    }


    public function composeMyOrders()
    {
        view()->composer('pages.account.my_orders', function ($view) {
            $view->with('buyer', \Auth::user());
        });
    }


    public function composeMyJobs()
    {
        view()->composer('pages.account.my_jobs', function ($view) {
            $view->with('employee', \Auth::user());
        });
    }

    public function composeMessages()
    {
        view()->composer('pages.messages', function ($view) {
            $view->with('userData', \Auth::user());
        });
    }
}
