<?php


namespace App\Http\Controllers;


use App\Models\Article;
use App\Models\Discipline;
use App\Models\FocusArea;
use App\Models\Keyword;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Log;
use Symfony\Component\VarDumper\VarDumper;

class TestController extends Controller {

    public function index() {
        VarDumper::dump('Test DATA');
        $services = Service::all();

        foreach($services as $service) {
            $keywords = Keyword::all()->random(3);
            $focusArea = FocusArea::all()->random(2);
            $disciplines = Discipline::published()->get()->random(2);
            $serviceType = ServiceType::all()->random(1)->first();
            $articles = Article::published()->get()->random(2);
            $service->focus_areas()->sync($focusArea);
            $service->keywords()->sync($keywords);
            $service->disciplines()->sync($disciplines);
            $service->service_type()->associate($serviceType);
            $service->articles()->sync($articles);
            $service->service_type_id = $serviceType->id;
            $service->save();
        }
        Log::info('Test controller');
    }

    public function index2() {
        $mediaVideos = collect([
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/06/30/airplane.MP4'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/06/30/above_beyond_feat_richard_bedford_sun_moon_official_music_video.mp4'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/10/22/test_alt_23.mp4'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/06/30/jellyfish_1080_10s_1mb.mp4']
                               ]);
        $mediaImages = collect([
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/06/14/Annotation 2020-03-20 225716_20200614205748.png'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/05/28/p_20160619_161446_df.jpg'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/06/13/445660o_1d2ghpcuo1k8mdi61ds51ks100gq.jpg'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/06/13/polet_cheshirskogo_kota.jpg'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/05/26/yellow_sofa_in_sunny_living_room.jpg'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/05/26/cyclist_on_the_road.jpg'],
                                   ['url' => 'https://dev.dl.splento.com/cdn/2020/05/20/desktop_backadsfasd.jpg'],
                               ]);
        $mediaFiles = collect([
                                  ['url' => 'https://calibre-ebook.com/downloads/demos/demo.docx'],
                                  ['url' => 'http://www.orimi.com/pdf-test.pdf'],
                                  ['url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
                                  ['url' => 'https://www.cmu.edu/blackboard/files/evaluate/tests-example.xls']
                              ]);
        $articles = Service::all();
        foreach ($articles as $article) {
            $article->media_videos()->delete();
            $article->media_images()->delete();
            $article->media_files()->delete();

            $video = $mediaVideos->random(2)->toArray();
            $images = $mediaImages->random(5)->toArray();
            $files = $mediaFiles->random(2)->toArray();

            $article->media_videos()->createMany($video);
            $article->media_images()->createMany($images);
            $article->media_files()->createMany($files);
        }
    }



}
