<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Camera;
use App\Models\CameraHasGroup;
use App\Models\CamerasGroupHasUser;
use App\Models\UserHasCamera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Stream\Utils;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;
use DateTime;

class CameraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Camera::where(function($query)
        {
            $query->whereIn('id', UserHasCamera::select('camera_id')->where('user_id', Auth::user()->id))
                  ->orWhereIn('id', CameraHasGroup::select('camera_id')->whereIn('group_id', CamerasGroupHasUser::select('group_id')->where('user_id', Auth::user()->id)));
        });
        //->whereIn('id', Auth::user()->cameras);
        //$list = Camera::select()->whereIn('id', Auth::user()->cameras);
        //->belongsToMany(Camera::class, 'user_has_cameras', 'user_id', 'camera_id')
        
        if (isset($request->sortColumn) && isset($request->sortDirection))
            $list = $list->orderBy($request->sortColumn, $request->sortDirection === 'ascending' ? 'asc' : 'desc');
        if (isset($request->findText)) {
            $list = $list->orWhere('name', 'like', '%' . $request->findText . '%');
        }
        if(isset($request->group) && $request->group!=='0') 
            $list = $list->whereIn('id', CameraHasGroup::select('camera_id')->where('group_id', $request->group));
        $list = (isset($request->all) && $request->all==='true') ? $list->paginate(999999) : $list->paginate(12);

        return response()->json(
            $list,
            200
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $camera = new Camera([
            'name' => $data['name'],
            'description' => $data['description'],
            'stream' => $data['stream'],
            'substream' => $data['substream'],
        ]);
        $camera->save();

        $translitName = $this->translit($data['name']);

        /*if(isset($data['substream'])){
            Storage::disk('video')->makeDirectory($translitName.'/Stream');
            file_put_contents('/etc/supervisor/conf.d/ffmpeg-'.$translitName.'-stream.conf' , 
                "[program:ffmpeg-".$translitName."-stream]\n"
                ."command=bash /etc/supervisor/script/start-stream-hls.sh \"".$data['substream']."\" \"".str_replace("//", "/",Storage::disk('video')->path($translitName))."/Stream\"\n"
                ."autostart=true\n"
                ."autorestart=true\n"
                ."user=root"
            );
        }

        /*if(isset($data['stream'])){
            Storage::disk('video')->makeDirectory($translitName.'/Archive');
            file_put_contents('/etc/supervisor/conf.d/ffmpeg-'.$translitName.'-archive.conf' , 
                "[program:ffmpeg-".$translitName."-archive]\n"
                ."command=bash /etc/supervisor/script/start-stream-mp4.sh \"".$data['stream']."\" \"".str_replace("//", "/",Storage::disk('video')->path($translitName))."/Archive\"\n"
                ."autostart=true\n"
                ."autorestart=true\n"
                ."user=root"
            );
        }
        exec("supervisorctl update", $output);*/
        return response()->json(
            $camera,
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Camera  $camera
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get("http://localhost:8080/$id/config/list");
        $camera_conf = [];
        if ($request->getStatusCode() == 200) {
            $response = $request->getBody();
            $camera_conf_arr = explode("\n", $response);
            foreach ($camera_conf_arr as $param) {
                $arr = explode("=", $param);
                if (isset($arr[0]) && isset($arr[1]))
                    $camera_conf[trim($arr[0])] = trim($arr[1]);
            }
        }

        $camera = Camera::find($id);

        return response()->json([
                'camera' => $camera, 
                'camera_conf' => $camera_conf
            ],
            200
        );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Camera  $camera
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*
        {
	"camera": {
		"id": 6,
		"name": "Красновишерск завод 5",
		"description": "Красновишерск завод 5",
		"status": "off",
		"login": "admin",
		"password": "3ap48765",
		"public": 0,
		"width": 352,
		"height": 288,
	},
	"camera_conf": {
		"camera_name": "Красновишерск завод 5",
		"camera_id": "6",
		"netcam_url": "rtsp://admin:3ap48765@10.200.5.187:554/Streaming/Channels/102?transportmode",
		"netcam_high_url": "rtsp://admin:3ap48765@10.200.5.187:554/Streaming/Channels/101?transportmode",
		"netcam_userpass": "(null)",
		"width": "352",
		"height": "288",
	}
}
        */
        //$url = "http://localhost:8080/$id/config/set?netcam_url=$request->substream&netcam_high_url=$request->stream&netcam_userpass=$request->login:$request->password";
        //        var_dump($url); die;

        $client = new \GuzzleHttp\Client();

        /*$client->get("http://localhost:8080/0/config/set?event_gap=60");
        $client->get("http://localhost:8080/0/config/set?pre_capture=5");
        $client->get("http://localhost:8080/0/config/set?post_capture=1500");
        $client->get("http://localhost:8080/0/config/set?movie_max_time=0");
        $client->get("http://localhost:8080/0/config/write");*/

        $client->get(htmlspecialchars("http://localhost:8080/$id/config/set?camera_name=$request->name"));
        $client->get(htmlspecialchars("http://localhost:8080/$id/config/set?netcam_url=$request->substream"));
        $client->get(htmlspecialchars("http://localhost:8080/$id/config/set?netcam_high_url=$request->stream"));
        $client->get(htmlspecialchars("http://localhost:8080/$id/config/set?netcam_userpass=$request->login:$request->password"));
        $client->get(htmlspecialchars("http://localhost:8080/$id/config/set?width=$request->width"));
        $client->get(htmlspecialchars("http://localhost:8080/$id/config/set?height=$request->height"));

        $client->get("http://localhost:8080/$id/config/write");
        $client->get("http://localhost:8080/$id/action/restart");
        $client->get("http://localhost:8080/$id/action/snapshot");
        
        $camera = Camera::find($id);

        $camera->name = $request->name;
        $camera->description = $request->description;
		$camera->login = $request->login;
		$camera->password = $request->password;
		$camera->substream = $request->substream;
		$camera->stream = $request->stream;
		$camera->width = $request->width;
		$camera->height = $request->height;

        $camera->save();

        return response()->json([
                'camera' => $camera, 
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Camera  $camera
     * @return \Illuminate\Http\Response
     */
    public function destroy(Camera $camera)
    {
        //
    }

    public function stream_index(Request $request, $id, $file)
    {
        $camera = Camera::find($id);

        return file_get_contents(str_replace("//", "/", Storage::disk('video')->path($id)) . "/stream/$file");
    }

    public function stream(Request $request, $id, $quality, $file)
    {
        $camera = Camera::find($id);
        return file_get_contents(str_replace("//", "/", Storage::disk('video')->path($id)) . "/stream/$quality/$file");
    }

    public function snapshot(Request $request, $id)
    {
        $camera = Camera::find($id);
        return file_get_contents(str_replace("//", "/", Storage::disk('video')->path($id)) . "/snapshot.jpg");
    }

    public function archive(Request $request, $id)
    {
        if ((Auth::user()->id == 44 && $id == 101) || (Auth::user()->id !== 44))

            $camera = Camera::find($id);
        $files = Storage::disk('video')->files($id . '/archive');

        $items = [];

        foreach ($files as $file) {
            if (isset($request->from) && isset($request->to)) {
                $dt = substr(explode('/', $file)[2], 0, 15);
                $fileData = DateTime::createFromFormat('Ymd_His', $dt);
                if (($fileData >= DateTime::createFromFormat('Y-m-dGi', $request->from)) && ($fileData <= DateTime::createFromFormat('Y-m-dGi', $request->to))) {
                    $items[] = [
                        'size' => Storage::disk('video')->size($file),
                        'src'  => $file,
                    ];
                }
            } else {
                $items[] = [
                    'size' => Storage::disk('video')->size($file),
                    'src'  => $file,
                ];
            }
        }

        $items = array_reverse($items);

        $page = isset($request->page) ? $request->page : 1; // Get the page=1 from the url
        $perPage = 15; // Number of items per page
        $offset = ($page * $perPage) - $perPage;

        $entries =  new LengthAwarePaginator(
            array_slice($items, $offset, $perPage, false),
            count($items), // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return response()->json(
            $entries,
            200
        );
    }

    public function archivefile(Request $request, $id, $file)
    {
        $camera = Camera::find($id);
        return file_get_contents(str_replace("//", "/", Storage::disk('video')->path($id)) . "/archive/" . $file);
    }

    private function translit($s)
    {
        $s = (string) $s; // преобразуем в строковое значение
        $s = strip_tags($s); // убираем HTML-теги
        $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
        $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
        $s = trim($s); // убираем пробелы в начале и конце строки
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        $s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
        $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
        return $s; // возвращаем результат
    }
}
