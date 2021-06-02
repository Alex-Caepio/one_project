<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PublishRequest;
use App\Models\Schedule;
use App\Models\User;
use DB;
use App\Http\Requests\Request;


class QuoteController extends Controller {

    public function quotesArticles(Request $request) {
        $articlesCount = $request->user()->articles()->count();

        if ($request->user()->isFullyRestricted() === false) {

            $article_publishing = $request->user()->plan->article_publishing;

            if ($request->user()->plan->article_publishing_unlimited) {
                $quotes = [
                    'allowed' => true,
                    'current' => $articlesCount,
                    'max'     => null,
                    'message' => null
                ];

            } elseif ($articlesCount < $article_publishing) {
                $quotes = [
                    'allowed' => true,
                    'current' => $articlesCount,
                    'max'     => $article_publishing,
                    'message' => null
                ];
            } elseif ($articlesCount >= $article_publishing) {
                $quotes = [
                    'allowed' => false,
                    'current' => $articlesCount,
                    'max'     => $article_publishing,
                    'message' => "You\'ve already reached your limit of {$article_publishing} articles"
                ];
            }

            return $quotes;

        }

        return [
            'allowed' => false,
            'current' => $articlesCount,
            'max'     => null,
            'message' => 'You\'re no allowed to publish an article'
        ];

    }

    public function quotesServices(Request $request) {
        $schedulesCount = $request->service->schedules()->count();

        if ($request->user()->isFullyRestricted() === false) {

            $schedulesPerService = $request->user()->plan->schedules_per_service;
            if ($request->user()->plan->schedules_per_service_unlimited) {
                $quotes = [
                    'allowed' => true,
                    'current' => $schedulesCount,
                    'max'     => null,
                    'message' => null
                ];

            } elseif ($schedulesCount < $schedulesPerService) {
                $quotes = [
                    'allowed' => true,
                    'current' => $schedulesCount,
                    'max'     => $schedulesPerService,
                    'message' => null
                ];
            } elseif ($schedulesCount >= $schedulesPerService) {
                $quotes = [
                    'allowed' => false,
                    'current' => $schedulesCount,
                    'max'     => $schedulesPerService,
                    'message' => "You\'ve already reached your limit of {$schedulesPerService} schedules"
                ];
            }

            return $quotes;

        }

        return [
            'allowed' => false,
            'current' => $schedulesCount,
            'max'     => null,
            'message' => 'You\'re no allowed to publish an schedules'
        ];
    }

    public function quotesPrices(Request $request) {
        $schedule = Schedule::find($request->schedule);
        $pricesCount = $schedule->prices()->count();

        if ($request->user()->isFullyRestricted() === false) {

            $pricingOptionsPerService = $request->user()->plan->pricing_options_per_service;

            if ($request->user()->plan->pricing_options_per_service_unlimited) {
                $quotes = [
                    'allowed' => true,
                    'current' => $pricesCount,
                    'max'     => null,
                    'message' => null
                ];

            } elseif ($pricesCount < $pricingOptionsPerService) {
                $quotes = [
                    'allowed' => true,
                    'current' => $pricesCount,
                    'max'     => $pricingOptionsPerService,
                    'message' => null
                ];
            } elseif ($pricesCount >= $pricingOptionsPerService) {
                $quotes = [
                    'allowed' => false,
                    'current' => $pricesCount,
                    'max'     => $pricingOptionsPerService,
                    'message' => "You\'ve already reached your limit of {$pricingOptionsPerService} prices"
                ];
            }

            return $quotes;

        } else {
            return [
                'allowed' => false,
                'current' => $pricesCount,
                'max'     => null,
                'message' => 'You\'re no allowed to publish an prices'
            ];
        }
    }

}
