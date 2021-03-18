<?php

namespace App\Http\Controllers;

use App\Library\FOApiClient;
use App\Models\Post;
use App\Models\BlogPost;
use App\Models\Service;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;

class SearchController extends PublicController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function search()
    {
        $input = Input::all();
        $search = $input['q'];
        $filters = [
            'sort' => json_encode([
                ['field' => 'created_at', 'orderBy' => 'desc'],
            ])
        ];

        if (!$search) {
            return View::make($this->template->getView('searchResult'));
        }

        if (strlen($search) < 3) {
            return View::make($this->template->getView('searchResult')
                ->with('str_limit', 1)
                ->with('search', $search));
        }

        $results_count = 0;

        $ArticlePosts = $this->getArticles($search);
        $results_count += count($ArticlePosts);

        $BlogPosts = $this->getBlogPosts($search);
        $results_count += count($BlogPosts);

        $Services = $this->getServices($search);
        $results_count += count($Services);

        $Promos = $this->getPromos($search);
        $results_count += count($Promos);

        $Events = $this->getEvents($search);
        $results_count += count($Events);

        $Packs = $this->getPacks($search);
        $results_count += count($Packs);

        $News = $this->getNews($search);
        $results_count += count($News);

        $Locations = $this->getLocations($search);
        $results_count += count($Locations);

        return View::make($this->template->getView('searchResult')
            ->with('posts', !empty($ArticlePosts) ? $ArticlePosts : null)
            ->with('blog_posts', !empty($BlogPosts) ? $BlogPosts : null)
            ->with('services', !empty($Services) ? $Services : null)
            ->with('promos', !empty($Promos) ? $Promos : null)
            ->with('events', !empty($Events) ? $Events : null)
            ->with('packs', !empty($Packs) ? $Packs : null)
            ->with('news', !empty($News) ? $News : null)
            ->with('locations', !empty($Locations) ? $Locations : null)
            ->with('found', !empty($results_count) ? $results_count : null)
            ->with('search', !empty($search) ? $search : null));
    }

    public function result()
    {
        return View::make($this->template->getView('searchResult'));
    }

    private function getArticles($search)
    {
        return DB::table('post')
            ->join('post_translations', 'post.id', '=', 'post_translations.post_id')
            ->where('post.status', 1)
            ->where('post_translations.locale', LaravelLocalization::getCurrentLocale())
            ->where(function ($query) use ($search) {
                $query->where('post_translations.title', 'like', "%$search%")
                    ->orWhere('post_translations.description', 'like', "%$search%");
            })
            ->select('post.*', 'post_translations.*')
            ->get();
    }

    private function getBlogPosts($search)
    {
        if (!empty(Config::get('settings.pharmacy.use_new_contents=.' . FOApiClient::CONTENT_TYPE_BLOG))) {
            $apiClient = new FOApiClient();
            return $apiClient->getActiveContents($this->common['pharmacy']->id, FOApiClient::CONTENT_TYPE_BLOG, ['search' => $search]);
        }

        return DB::table('blog_post')
            ->join('blog_post_translations', 'blog_post.id', '==', 'blog_post_translations.blog_post_id')
            ->where('blog_post.status', 1)
            ->where('blog_post_translations.locale', LaravelLocalization::getCurrentLocale())
            ->where(function ($query) use ($search) {
                $query->where('blog_post_translations.title', 'like', "%$search%")
                    ->orWhere('blog_post_translations.description', 'like', "%$search%")
                    ->orWhere('blog_post_translations.short_description', 'like', "%$search%");
            })
            ->select('blog_post.*', 'blog_post_translations.*')
            ->get();
    }

    private function getServices($search)
    {
        if (!empty(Config::get('settings.pharmacy.use_new_contents=.' . FOApiClient::CONTENT_TYPE_SERVICE))) {
            $apiClient = new FOApiClient();
            $content = $apiClient->getContentsByFilter($this->common['pharmacy']->id, FOApiClient::CONTENT_TYPE_SERVICE, $filters, FOApiClient::DEFAULT_PAGE, FOApiClient::DEFAULT_LIMIT, $search);
            return $content['data'];
        }

        return DB::table('service')
            ->join('service_translations', 'service.id', '=', 'service_translations.service_id')
            ->where('service.status', 1)
            ->whereNotNull('service.parent_id')
            ->where('service_translations.locale', LaravelLocalization::getCurrentLocale())
            ->where(function ($query) use ($search) {
                $query->where('service_translations.title', 'like', "%$search%")
                    ->orWhere('service_translations.description', 'like', "%$search%");
            })
            ->select('service.*', 'service_translations.*')
            ->get();
    }

    private function getPromos($search)
    {
        if (!empty(Config::get('settings.pharmacy.use_new_contents=.' . FOApiClient::CONTENT_TYPE_PROMO))) {
            $apiClient = new FOApiClient();
            return $apiClient->getActiveContents($this->common = ['pharmacy']->id, FOApiClient::CONTENT_TYPE_PROMO, ['search' => $search]);
        }

        return DB::table('promo')
            ->join('promo_translations', 'promo.id', '=', 'promo_translations.promo_id')
            ->where('promo.status', 1)
            ->where('promo_translations.locale', LaravelLocalization::getCurrentLocale())
            ->whereRaw('(date_ini<SYSDATE() OR date_ini IS NULL)')
            ->whereRaw('(date_end>=SYSDATE() OR date_end IS NULL)') // No lo he cambiado ya que no entra ninguna variable y no puede haber SQL Injection
            ->where(function ($query) use ($search) {
                $query->where('promo_translations.title', 'like', "%$search%")
                    ->orWhere('promo_translations.description', 'like', "%$search%");
            })
            ->select('promo.*', 'promo_translations.*')
            ->get();
    }

    private function getEvents($search)
    {
        if (!empty(Config::get('settings.pharmacy.use_new_contents=.' . FOApiClient::CONTENT_TYPE_EVENT))) {
            $apiClient = new FOApiClient();
            $content = $apiClient->getContentsByFilter($this->common['pharmacy']->id, FOApiClient::CONTENT_TYPE_EVENT, $filters, FOApiClient::DEFAULT_PAGE, FOApiClient::DEFAULT_LIMIT, $search);
            return $content['data'];
        }

        return DB::table('event')
            ->join('event_translations', 'event.id', '=', 'event_translations.event_id')
            ->where('event.status', 1)
            ->where('event_translations.locale', LaravelLocalization::getCurrentLocale())
            ->where(function ($query) use ($search) {
                $query->where('event_translations.title', 'like', "%$search%")
                    ->orWhere('event_translations.description', 'like', "%$search%");
            })
            ->select('event.*', 'event_translations.*')
            ->setBindings(['search' => $search])
            ->get();
    }

    private function getPacks($search)
    {
        if (!empty(Config::get('settings.pharmacy.use_new_contents=.' . FOApiClient::CONTENT_TYPE_PACKS))) {
            $apiClient = new FOApiClient();
            $content = $apiClient->getContentsByFilter($this->common['pharmacy']->id, FOApiClient::CONTENT_TYPE_PACKS, $filters, FOApiClient::DEFAULT_PAGE, FOApiClient::DEFAULT_LIMIT, $search);
            return $content['data'];
        }

        return [];
    }

    private function getNews($search)
    {
        if (!empty(Config::get('settings.pharmacy.use_new_contents=.' . FOApiClient::CONTENT_TYPE_NEWS))) {
            $apiClient = new FOApiClient();
            $content = $apiClient->getContentsByFilter($this->common['pharmacy']->id, FOApiClient::CONTENT_TYPE_NEWS, $filters, FOApiClient::DEFAULT_PAGE, FOApiClient::DEFAULT_LIMIT, $search);
            return $content['data'];
        }

        return [];
    }

    private function getLocations($search)
    {
        if (!empty(Config::get('settings.pharmacy.use_new_contents=.' . FOApiClient::CONTENT_TYPE_LOCATIONS))) {
            $apiClient = new FOApiClient();
            $content = $apiClient->getContentsByFilter($this->common['pharmacy']->id, FOApiClient::CONTENT_TYPE_LOCATIONS, $filters, FOApiClient::DEFAULT_PAGE, FOApiClient::DEFAULT_LIMIT, $search);
            return $content['data'];
        }

        return [];
    }
}
