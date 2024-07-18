<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWebsiteRequest;
use App\Http\Requests\UpdateWebsiteRequest;
use App\Http\Resources\WebsiteResource;
use App\Http\Resources\VoteResource;
use App\Models\Category;
use App\Models\Website;
use App\QueryBuilder\Sorts\CategoriesCount;
use App\QueryBuilder\Sorts\VotesCount;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;


class WebsiteController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        /**
         * Determine if the user is authorized to make CRUD operation for the resource.
         */
        $this->authorizeResource(Website::class, 'website');
    }
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection 
     */
    public function index()
    {
        $per_page = is_numeric(request()->per_page)? (int) request()->per_page : 15;

        $websites = QueryBuilder::for(Website::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'name',
            'url',
            'description',
            'created_at',
            'updated_at',
            AllowedSort::custom('votesCount', new VotesCount()),
            AllowedSort::custom('categoriesCount', new CategoriesCount()),
        )
        ->allowedFilters([
            'name',
            'url',
            'description',
            'created_at',
            'updated_at'
        ])
        ->allowedIncludes([
            AllowedInclude::count('votesCount'), 
            AllowedInclude::exists('votesExists'),
            AllowedInclude::count('categoriesCount'), 
            AllowedInclude::exists('categoriesExists'),
        ]);

         //  Perform search query
         if(request()->has('q'))
         {
             $q = request()->q;
 
             $websites->where(function(Builder $query) use($q){
 
                 // search by website fillable attributes
                 $web = new Website();
                 $web_fillables = $web->getFillable();
 
                 $counter = 0; 
                 foreach ($web_fillables as $key => $fillable) {
                     if($counter==0) $query->where($fillable, 'like',  '%'.$q.'%');
                     else $query->orWhere($fillable, 'like',  '%'.$q.'%');
                     $counter++;
                 }
 
                 // search website by categories
                 $query->orWhereHas('categories', function(Builder $query) use($q){
                     $cat = new Category();
                     $cat_fillables = $cat->getFillable();
                     $cat_counter = 0; 
                     foreach ($cat_fillables as $key => $fillable) {
                         if($cat_counter==0) $query->where($fillable, 'like',  '%'.$q.'%');
                         else $query->orWhere($fillable, 'like',  '%'.$q.'%');
                         $cat_counter++;
                     }
 
                 });
             });
          
            
         };

        $websites = $websites->paginate($per_page)
        ->appends(request()->query());

        $website_resource =  WebsiteResource::collection($websites);

        $website_resource->with['status'] = "OK";
        $website_resource->with['message'] = 'Websites retrived successfully';

        return $website_resource;
    }


    /**
     * Store a newly created resource in storage.
     * 
     * @param \App\Http\Requests\StoreWebsiteRequest $request
     * 
     * @return \App\Http\Resources\WebsiteResource
     */
    public function store(StoreWebsiteRequest $request)
    {
        $validated = $request->validated();

        $website = Website::create($validated);

        $website->categories()->attach($validated['category_ids'] ?? []);
        $website->categories()->paginate();

        $website_resource = new WebsiteResource($website);
        $website_resource->with['message'] = 'Website created successfully';
 
        return  $website_resource;
    }

    /**
     * Display the specified resource.
     * 
     * @param \App\Models\Website $website
     * 
     * @return \App\Http\Resources\WebsiteResource
     */
    public function show(Website $website)
    {
        /**
         * Include website relationship on run time if demanded 
         */
       if(request()->has('include'))
       {
           foreach (explode(',', request()->include) as $key => $value) 
           {
                if(method_exists($website, $value))
                {
                    $website->load($value);
                }
                elseif(str_contains($value, 'Count') && method_exists($website, $relationship=Str::before($value, 'Count')))
                {
                    $website->loadCount($relationship);
                }

                elseif(str_contains($value, 'Exists') && method_exists($website, $relationship=Str::before($value, 'Exists')))
                {
                    $website->loadExists($relationship);
                }
           }
       }

       /**
        * Append website attributes instance on run time  demanded
        */
       if(request()->has('append'))
       {
           foreach (explode(',', request()->append) as $key => $value) {
              $website->append($value);
           }
       }
           

       $website_resource = new WebsiteResource($website);
       $website_resource->with['message'] = 'Website retrieved successfully';

       return  $website_resource;
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param \App\Http\Requests\UpdateWebsiteRequest $request
     * @param \App\Models\Website $website
     */
    public function update(UpdateWebsiteRequest $request, Website $website)
    {
        $validated = $request->validated();
        $website->update($request->validated());

        $website->categories()->syncWithoutDetaching($validated['category_ids'] ?? []);

        $website_resource = new WebsiteResource($website);
        $website_resource->with['message'] = 'Website updated successfully';
 
        return  $website_resource;
    }

    /**
     * Vote the specified resource in storage.
     * 
     * @param \App\Models\Website $website
     */
    public function vote(Website $website)
    {
        $vote = $website->votes()->firstOrCreate(['user_id' => auth()->user()->id]);
        $vote->load(['votable', 'user']);

        $vote_resource = new VoteResource($vote);
        $vote_resource->with['message'] = 'Website voted successfully';
 
        return  $vote_resource;
    }

    /**
     * unvote the specified resource in storage.
     * 
     * @param \App\Models\Website $website
     */
    public function unvote(Website $website)
    {
        $website->votes()
        ->where('user_id', auth()->user()->id)
        ->delete();

        $vote_resource = new VoteResource(null);
        $vote_resource->with['message'] = 'Website unvote successfully';
 
        return  $vote_resource;
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param \App\Models\Website $website
     */
    public function destroy(Website $website)
    {
        $website->delete();

        $website_resource = new WebsiteResource(null);
        $website_resource->with['message'] = 'Website deleted successfully';
 
        return  $website_resource;
    }
}
