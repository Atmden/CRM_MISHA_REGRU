<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Account\AccountResource;
use App\Http\Resources\Account\AccountToEditResource;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Plan\FilterPlansResource;
use App\Http\Resources\Plan\GetOnePlanResource;
use App\Http\Resources\Plan\GetPlansResource;
use App\Http\Resources\Plan\GetSocnetsResource;
use App\Http\Resources\Plan\PlanFoAccountResource;
use App\Http\Resources\Plan\PlanForEditResource;
use App\Http\Resources\Socnet\SocnetResource;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Plan;
use App\Models\Tag;
use App\Models\TempImage;
use App\Models\TempVideo;
use App\Models\TempYoutube;
use App\Models\User;
use App\Models\Userfilter;
use App\Models\Video;
use App\Models\Youtube;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Socnet;
use App\Models\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\CopyFormat;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Symfony\Component\Process\Process;
use Validator;
use Intervention\Image\Facades\Image as ImageI;

class PlanController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function set_public_auto()
    {
        $plans = Plan::where('status_id', '3')->get();
    }

    public function save_comment_plan(Request $request)
    {
        $plan_id = $request->plan_id;
        $content = $request->comment;
        $user_id = Auth::id();
        $comment = new Comment();
        $comment->plan_id = $plan_id;
        $comment->user_id = $user_id;
        $comment->content = $content;
        $comment->apply = false;
        $comment->save();
        $comments = Comment::where('plan_id', $plan_id)->get();
        return $this->sendResponse(CommentResource::collection($comments), 'Правка добавлена');
    }

    public function delete_comment(Request $request)
    {
        $plan_id = $request->plan_id;
        $comment_id = $request->comment_id;

        $user_id = Auth::id();
        $comment = Comment::where('id',$comment_id)->where('user_id',$user_id)->delete();

        $comments = Comment::where('plan_id', $plan_id)->get();
        return $this->sendResponse(CommentResource::collection($comments), 'Правка удалена');
    }

    public function apply_comment_plan(Request $request)
    {
        if (isset($request->comment_id)) {
            $comment = Comment::where('id', $request->comment_id)->update(['apply' => $request->apply]);
            return $this->sendResponse($comment, 'Изменения сохранены');
        }
    }

    public function get_comments_plan($id)
    {
        $comments = Comment::where('plan_id', $id)->get();

        return $this->sendResponse(CommentResource::collection($comments), 'Comments retrieved successfully.');
    }

    public function save_edit_plan(Request $request)
    {
        $plan = Plan::find($request->plan_id);
        $plan->title = $request->title;
        $plan->content = $request->content;
        $plan->hashtags = $request->hashtags;
        $plan->status_id = $request->status['id'];
        $plan->insta = $request->insta;
        $dateTimeString = $request->public_at . " " . $request->time_at;
        $dueDateTime = Carbon::createFromFormat('d/m/Y H:i', $dateTimeString, 'UTC');
        $plan->public_at = $dueDateTime;
        $plan->save();

        $new_tags = [];
        $plan_tags = $request->tags;
        foreach ($plan_tags as $plan_tag) {
            $find = Tag::where('name', $plan_tag['text'])->first();
            if ($find == null) {
                $new_tag = new Tag();
                $new_tag->name = $plan_tag['text'];
                $new_tag->save();
                $new_tags[] = $new_tag->id;
            } else {
                $new_tags[] = $find->id;
            }

        }
        $plan->tags()->sync($new_tags);
        $plan->socnets()->sync($request->soc_net);

        //21.06.2021 - сортировка фото
        if ($request->upload_images_main != null) {
            $images = $request->upload_images_main['images'];
            $i = 0;
            foreach ($images as $item) {
                $i++;
                $image = Image::where('file', $item['file'])->first();
                $image->order_image = $i;
                $image->save();
            }
        }

        //22.06.2021 - сортировка видео
        if ($request->upload_videos_main != null) {
            $videos = $request->upload_videos_main['videos'];
            $i = 0;
            foreach ($videos as $item) {
                $i++;
                $image = Video::where('file', $item['file'])->first();
                $image->order_video = $i;
                $image->save();
            }
        }
        return $this->sendResponse($plan->toArray(), 'Изменения сохранены');
    }

    public function add_new_plan(Request $request)
    {

        $plan = new Plan();
        $plan->title = $request->title;
        $plan->hashtags = $request->hashtags;
        $plan->content = $request->content;
        if (isset($request->status['id'])) {
            $plan->status_id = $request->status['id'];
        }
        else
        {
            $plan->status_id = 1;
        }
        $plan->insta = $request->insta;
        $plan->account_id = $request->account_id;

        $dateTimeString = $request->public_at . " " . $request->time_at;
        $dueDateTime = Carbon::createFromFormat('d/m/Y H:i', $dateTimeString, 'UTC');
        $plan->public_at = $dueDateTime;
        $plan->save();

        $plan_tags = $request->tags;

        foreach ($plan_tags as $plan_tag) {
            $find = Tag::where('name', $plan_tag['text'])->first();
            if ($find == null) {
                $new_tag = new Tag();
                $new_tag->name = $plan_tag['text'];
                $new_tag->save();
                $plan->tags()->attach($new_tag->id);
            } else {
                $plan->tags()->attach($find->id);
            }
        }

        $plan_soc_nets = $request->soc_net;
        foreach ($plan_soc_nets as $plan_soc_net) {
            $plan->socnets()->attach($plan_soc_net);
        }

        // добавляем фото
//        $tempImages = TempImage::where('random_number', $request->random_number)->get();
//
//        foreach ($tempImages as $tempImage) {
//            $image = new Image();
//            $image->file = $tempImage->file;
//            $image->plan_id = $plan->id;
//            $image->save();
//        }

        // Изменено 21/06/2021 для реализации сортировки ФОТО
        if ($request->upload_images_main != null) {
            $images = $request->upload_images_main['images'];
            $i = 0;
            foreach ($images as $item) {
                $i++;
                $image = new Image();
                $image->file = $item['file'];
                $image->plan_id = $plan->id;
                $image->order_image = $i;
                $image->save();
            }
        }

        $tempImages = TempImage::where('random_number', $request->random_number)->delete();

        // добавляем видео
//        $tempVideos = TempVideo::where('random_number', $request->random_number)->get();
//
//        foreach ($tempVideos as $tempVideo) {
//            $video = new Video();
//            $video->file = $tempVideo->file;
//            $video->plan_id = $plan->id;
//            $video->save();
//        }

        // Изменено 22/06/2021 для реализации сортировки Видео
        if ($request->upload_videos_main != null) {
            $videos = $request->upload_videos_main['videos'];
            $i = 0;
            foreach ($videos as $item) {
                $i++;
                $image = new Video();
                $image->file = $item['file'];
                $image->plan_id = $plan->id;
                $image->order_video = $i;
                $image->save();
            }
        }

        $tempVideos = TempVideo::where('random_number', $request->random_number)->delete();

        // добавляем youtube
        $tempYoutubes = TempYoutube::where('random_number', $request->random_number)->get();

        foreach ($tempYoutubes as $tempYoutube) {
            $youtube = new Youtube();
            $youtube->url = $tempYoutube->url;
            $youtube->plan_id = $plan->id;
            $youtube->save();
        }
        $tempYoutubes = TempYoutube::where('random_number', $request->random_number)->delete();

        return $this->sendResponse($plan->toArray(), 'Контент план создан');
    }

    public function apply_plan($id)
    {
        $plan = Plan::find($id);
        $plan->status_id = 3;
        $plan->save();
        return $this->sendResponse($plan->toArray(), 'Контент план утвержден');
    }
    public function set_status_plan($id,$status_id)
    {
        $plan = Plan::find($id);
        $plan->status_id = $status_id;
        $plan->save();
        return $this->sendResponse($plan->toArray(), 'Статус изменен');
    }
    public function plan_images_delete($id)
    {

        $image = Image::find($id);

        $plan_id = $image->plan_id;

        if (file_exists(public_path() . '/plan_images/' . $image->file)) {
            unlink(public_path() . '/plan_images/' . $image->file);
        }


        $image->delete();

        $images = Image::where('plan_id', $plan_id)->get();

        return $this->sendResponse($images->toArray(), 'Images deleted successfully.');

    }

    public function plan_videos_delete($id)
    {

        $video = Video::find($id);

        $plan_id = $video->plan_id;

        if (file_exists(public_path() . '/plan_videos/' . $video->file)) {
            unlink(public_path() . '/plan_videos/' . $video->file);
        }


        $video->delete();

        $videos = Video::where('plan_id', $plan_id)->get();

        return $this->sendResponse($videos->toArray(), 'Videos deleted successfully.');

    }

    public function temp_youtube_delete($id)
    {
        $youtube = TempYoutube::find($id);
        $random_number = $youtube->random_number;


        $youtube->delete();

        $youtubes = TempYoutube::where('random_number', $random_number)->get();

        return $this->sendResponse($youtubes->toArray(), 'Youtubes deleted successfully.');
    }

    public function plan_youtube_delete($id)
    {
        $youtube = Youtube::find($id);
        $plan_id = $youtube->plan_id;
        $youtube->delete();

        $youtubes = Youtube::where('plan_id', $plan_id)->get();

        return $this->sendResponse($youtubes->toArray(), 'Youtubes deleted successfully.');
    }

    public function upload_images_delete($id)
    {

        $image = TempImage::find($id);
        $random_number = $image->random_number;

        if (file_exists(public_path() . '/plan_images/' . $image->file)) {
            unlink(public_path() . '/plan_images/' . $image->file);
        }


        $image->delete();

        $images = TempImage::where('random_number', $random_number)->get();

        return $this->sendResponse($images->toArray(), 'Images deleted successfully.');

    }

    public function upload_videos_delete($id)
    {

        $video = TempVideo::find($id);
        $random_number = $video->random_number;

        if (file_exists(public_path() . '/plan_videos/' . $video->file)) {
            unlink(public_path() . '/plan_videos/' . $video->file);
        }


        $video->delete();

        $videos = TempImage::where('random_number', $random_number)->get();

        return $this->sendResponse($videos->toArray(), 'Videos deleted successfully.');

    }

    public function add_youtube(Request $request)
    {
        if (isset($request->plan_id)) {
            $plan_id = $request->plan_id;
            if (isset($request->url_youtube)) {
                $tempYoutube = new Youtube();
                $tempYoutube->url = $request->url_youtube;
                $tempYoutube->plan_id = $plan_id;
                $tempYoutube->save();
            }

            $youtubes = Youtube::where('plan_id', $plan_id)->get();
        } else {
            $random_number = $request->random_number;
            if (isset($request->url_youtube)) {

                $tempYoutube = new TempYoutube();
                $tempYoutube->url = $request->url_youtube;
                $tempYoutube->random_number = $random_number;
                $tempYoutube->save();
            }
            $youtubes = TempYoutube::where('random_number', $random_number)->get();
        }


        return $this->sendResponse($youtubes->toArray(), 'Youtubes uploaded successfully.');

    }

    public function encode_video($name)
    {
        $temp = public_path('plan_videos_temp/') . $name;
        $new = uniqid() . '.mp4';
        $new_name = public_path('plan_videos/') . $new;
        exec('ffmpeg -i ' . $temp . ' -c:v libx264 -c:a copy  -preset faster -profile:v main -level 3 -r 25 -g 100  -ac 2 -movflags faststart -threads auto ' . $new_name);
        exec('ffmpeg -i ' . $new_name . ' -ss 00:00:05 -t 00:00:05 -vframes 1 ' . $new_name . '.png');
        unlink($temp);
        return $new;
    }

    public function upload_videos(Request $request)
    {
        if (isset($request->plan_id)) {
            $plan_id = $request->plan_id;
            if (count($request->videos)) {
                $k = 0;
                foreach ($request->videos as $item) {
                    $file = $item->hashName();
                    $item->move(public_path('plan_videos_temp'), $file);
                    $name = $this->encode_video($file);
                    $tempVideo = new Video();
                    $tempVideo->file = $name;
                    $tempVideo->plan_id = $plan_id;
                    $tempVideo->save();
                }
            }

            $videos = Video::where('plan_id', $plan_id)->get();
        } else {
            $random_number = $request->random_number;
            if (count($request->videos)) {
                $k = 0;
                foreach ($request->videos as $item) {
                    $file = $item->hashName();
                    $item->move(public_path('plan_videos_temp'), $file);
                    $name = $this->encode_video($file);
                    $tempVideo = new TempVideo();
                    $tempVideo->file = $name;
                    $tempVideo->random_number = $random_number;
                    $tempVideo->save();
                }
            }

            $videos = TempVideo::where('random_number', $random_number)->get();
        }
        return $this->sendResponse($videos->toArray(), 'Videos uploaded successfully.');

    }

    public function upload_images(Request $request)
    {

        if (isset($request->plan_id)) {
            $plan_id = $request->plan_id;
            if (count($request->images)) {
                $k = 0;
                foreach ($request->images as $item) {

                    $file = $item;
                    $name = $file->hashName();
                    //$item->move(public_path('plan_images'), $name);
                    $image = ImageI::make($file);
                    $image->
                    resize(640, null, function ($constraint) {
                        $constraint->upsize();
                        $constraint->aspectRatio();

                    })->save(public_path('plan_images') . '/' . $name, 80);

                    $tempImage = new Image();
                    $tempImage->file = $name;
                    $tempImage->plan_id = $plan_id;
                    $tempImage->save();
                }
            }

            $images = Image::where('plan_id', $plan_id)->get();
        } else {
            $random_number = $request->random_number;
            if (count($request->images)) {
                $k = 0;
                foreach ($request->images as $item) {

                    $file = $item;
                    $name = $file->hashName();
                    //$item->move(public_path('plan_images'), $name);

                    $image = ImageI::make($file);
                    $image->
                    resize(640, null, function ($constraint) {
                        $constraint->upsize();
                        $constraint->aspectRatio();

                    })->save(public_path('plan_images') . '/' . $name, 80);


                    $tempImage = new TempImage();
                    $tempImage->file = $name;
                    $tempImage->random_number = $random_number;
                    $tempImage->save();
                }
            }

            $images = TempImage::where('random_number', $random_number)->get();
        }
        return $this->sendResponse($images->toArray(), 'Images uploaded successfully.');

    }

    public function get_edit_plan($id)
    {
        $plan = Plan::find($id);
        return $this->sendResponse(new PlanForEditResource($plan), 'Plans retrieved successfully.');
    }

    public function account_plan_filter(Request $request)
    {
        // сохраняем фильтр
        $filter = Userfilter::updateOrCreate(
            ['user_id' => Auth::id()],
            ['user_id' => Auth::id(),
                'filter' => json_encode($request->all())]);


        $account = Account::find($request->account_id);
        return $this->sendResponse(new FilterPlansResource($account, $filter), 'Plans filtered successfully.');
    }

    public function get_plans($id)
    {
        $plans = Plan::where('account_id', $id)->get();

        if (is_null($plans)) {
            return $this->sendError('Plans not found.');
        }

        $account = Account::find($id);

        $filter = Userfilter::where('user_id', Auth::id())->first();
        if ($filter != null) {
            return $this->sendResponse(new FilterPlansResource($account, $filter), 'Plans filtered successfully.');
        }

        return $this->sendResponse(new GetPlansResource($account), 'Plans retrieved successfully.');

    }

    public function one_plan_page($id)
    {

        $plan = Plan::find($id);

        if (is_null($plan)) {
            return $this->sendError('Plan not found.');
        }

        return $this->sendResponse(new GetOnePlanResource($plan), 'Plans retrieved successfully.');

    }

    public function get_tags($id)
    {
        $plans = Plan::where('account_id', $id)->get();

        $tags = Tag::whereHas('plans', function ($q) use ($plans) {
            $q->whereIn('plans.id', $plans->pluck('id'));
        })->get();


        return $this->sendResponse($tags->toArray(), 'Tags retrieved successfully.');
    }

    public function get_socnets($id)
    {
        $account = Account::find($id);

        $socnets = $account->socnets()->where('account_socnet.online', '1')->get();

        return $this->sendResponse($socnets->toArray(), 'Socnets retrieved successfully.');

    }

    public function page_socnets($id)
    {
        $plan = Plan::find($id);
        $account = $plan->account;

        $socnets = $account->socnets()->where('account_socnet.online', '1')->get();

        return $this->sendResponse($socnets->toArray(), 'Socnets retrieved successfully.');

    }

    public function index()
    {
        $socnet = Socnet::all();

        return $this->sendResponse(SocnetResource::collection($socnet), 'Socnets retrieved successfully.');
    }

    public function my_accounts()
    {
        $user = Auth::user();

        return $this->sendResponse(AccountResource::collection($user->accounts), 'Accounts retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'inn' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $company = Account::create($input);

        return $this->sendResponse($company->toArray(), 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = Account::find($id);

        if (is_null($account)) {
            return $this->sendError('Account not found.');
        }

        return $this->sendResponse(AccountResource::make($account), 'Account retrieved successfully.');
    }

    public function account_to_edit($id)
    {
        $account = Account::find($id);

        if (is_null($account)) {
            return $this->sendError('Account not found.');
        }

        return $this->sendResponse(AccountToEditResource::make($account), 'Account retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $company)
    {
        $input = $request->all();

        $company->update($input);

        return $this->sendResponse($company->toArray(), 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $company)
    {
        $company->delete();

        return $this->sendResponse($company->toArray(), 'Account deleted successfully.');
    }

}
