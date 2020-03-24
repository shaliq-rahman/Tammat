<?php
/**
 * LaraClassified - Geo Classified Ads CMS
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\Arr;
use App\Http\Requests\ContactRequest;
use App\Models\City;
use App\Models\Page;
use App\Mail\FormSent;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Torann\LaravelMetaTags\Facades\MetaTag;

class PageController extends FrontController
{
    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($slug)
    {
        // Get the Page
        $page = Page::where('slug', $slug)->trans()->first();
        if (empty($page)) {
            abort(404);
        }
        view()->share('page', $page);
        view()->share('uriPathPageSlug', $slug);

        // Check if an external link is available
        if (!empty($page->external_link)) {
            return headerLocation($page->external_link);
        }

        // SEO
        $title = $page->title;
        $description = str_limit(str_strip($page->content), 200);

        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', $description);

        // Open Graph
        $this->og->title($title)->description($description);
        if (!empty($page->picture)) {
            if ($this->og->has('image')) {
                $this->og->forget('image')->forget('image:width')->forget('image:height');
            }
            $this->og->image(Storage::url($page->picture), [
                'width' => 600,
                'height' => 600,
            ]);
        }
        view()->share('og', $this->og);

        return view('pages.index');
    }
	
	public function index_app($slug)
    {
        \App::setLocale(request()->get('language') ?: 'en');
        // Get the Page
        $page = Page::where('slug', $slug)->trans()->first();
        return response()->json(['results'=>$page]); 
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contact()
    {
        // Get the Country's largest city for Google Maps
        $city = City::currentCountry()->orderBy('population', 'desc')->first();
        view()->share('city', $city);

        // Meta Tags
        MetaTag::set('title', getMetaTag('title', 'contact'));
        MetaTag::set('description', strip_tags(getMetaTag('description', 'contact')));
        MetaTag::set('keywords', getMetaTag('keywords', 'contact'));

        return view('pages.contact');
    }
//  public function maptest()
//     {
//         // Get the Country's largest city for Google Maps
//         // $city = City::currentCountry()->orderBy('population', 'desc')->first();
//         // view()->share('city', $city);

//         // Meta Tags
//         MetaTag::set('title', getMetaTag('title', 'map'));
//         MetaTag::set('description', strip_tags(getMetaTag('description', 'map')));
//         MetaTag::set('keywords', getMetaTag('keywords', 'map'));

//         return view('pages.maptest');
//     }
    /**
     * @param ContactRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function contactPost(ContactRequest $request)
    {
        // Store Contact Info
        /*Code Made by MonTech Team*/
        $contactForm = $request->all();
        $contactForm['country_code'] = config('country.code');
        $contactForm['country_name'] = config('country.name');
        $contactForm = Arr::toObject($contactForm);
        
        $email = "admin@dealnotdeal.com";

        
        // Send Contact Email
        try {
            if ($email) {
                $recipient = [
                    'email' => $email,
                    'name' => config('settings.app.name'),
                ];
                $recipient = Arr::toObject($recipient);
                Mail::send(new FormSent($contactForm, $recipient));
            } else {
                $admins = User::where('is_admin', 1)->get();
                if ($admins->count() > 0) {
                    foreach ($admins as $admin) {
                        Mail::send(new FormSent($contactForm, $admin));
                    }
                }
            }
            flash(t("Your message has been sent to our moderators. Thank you"))->success();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }
        
        
        //  try {
        //     if (config('settings.app.email')) {
        //         $recipient = [
        //             'email' => config('settings.app.email'),
        //             'name' => config('settings.app.name'),
        //         ];
        //         $recipient = Arr::toObject($recipient);
        //         Mail::send(new FormSent($contactForm, $recipient));
        //     } else {
        //         $admins = User::where('is_admin', 1)->get();
        //         if ($admins->count() > 0) {
        //             foreach ($admins as $admin) {
        //                 Mail::send(new FormSent($contactForm, $admin));
        //             }
        //         }
        //     }
        //     flash(t("Your message has been sent to our moderators. Thank you"))->success();
        // } catch (\Exception $e) {
        //     flash($e->getMessage())->error();
        // }
        

        return redirect(config('app.locale') . '/' . trans('routes.contact'));
    }
    
    
    
    public function PostContact(Request $request)
    {
        $contactForm = $request->all();
        $contactForm = Arr::toObject($contactForm);
        $email = "admin@dealnotdeal.com";
        
        if ($email) 
        {
            $recipient = [
                'email' => $email,
                'name' => 'Deal Not Deal',
            ];
            $recipient = Arr::toObject($recipient);
            Mail::send(new FormSent($contactForm, $recipient));
        } 
        else 
        {
            $admins = User::where('is_admin', 1)->get();
            if ($admins->count() > 0) {
                foreach ($admins as $admin) {
                    Mail::send(new FormSent($contactForm, $admin));
                }
            }
        }
        
        return response()->json([
            'success' => 1,
            'message'=>t("Your message has been sent to our moderators. Thank you"),
        ]);    

    }
    
    
    public function PageList(Request $request)
    {
        
        if(!empty($request->language_code)) 
        {
            $final = array(); 
        
            $getPageList = Page::where('translation_lang', '=', strtolower($request->language_code))->get();
            if(count($getPageList) > 0)
            {
                foreach($getPageList as $value)    
                {
                    $json['name']   = $value->name;
                    $json['title']  = $value->title;    
                    $json['slug']   = $value->slug;  
                    $json['type']   = $value->type;  
                    $final[]        = $json; 
                }
            
                return response()->json([
                    'success' => 1,
                    'results' => $final,
                ]);    
            }
            else
            {
                return response()->json([
                    'success' => 0,
                    'message'=>'Please add perfect language code',
                    ]);    
            }    
        }
        else
        {
            return response()->json([
                    'success' => 0,
                    'message'=>'Parameter Missing',
                    ]);    
        }

    }
    
    
    public function PageDetail(Request $request)
    {
        if(!empty($request->language_code) && !empty($request->slug)) 
        {
            
        
            $getPageList = Page::where('translation_lang', '=', strtolower($request->language_code))->where('slug', '=', $request->slug)->first();
            if(!empty($getPageList))
            {
                $json['success']   = 1;
                $json['name']   = $getPageList->name;
                $json['title']  = $getPageList->title;    
                $json['slug']   = $getPageList->slug;  
                $json['type']   = $getPageList->type;  
                $json['content']   = $getPageList->content;  
                return response()->json($json);    
            }
            else
            {
                return response()->json([
                    'success' => 0,
                    'message'=>'Please add perfect language code',
                    ]);    
            }    
        }
        else
        {
            return response()->json([
                    'success' => 0,
                    'message'=>'Parameter Missing',
                    ]);    
        }

    }
    
    
    
    
}
