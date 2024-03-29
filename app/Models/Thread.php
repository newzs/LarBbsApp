<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\RecordsActivity;
use App\Events\ThreadReceivedNewReply;
use Illuminate\Support\Str;

class Thread extends Model {
    use RecordsActivity;

    protected $guarded = []; // 意味所有属性均可更新，后期会修复此安全隐患
    protected $with = ['creator'];
    protected $appends = ['isSubscribedTo'];

    protected $casts = [
        'locked' => 'boolean'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    public function replies()
    {
        return $this->hasMany(Reply::class)
            ->withCount('favorites')
            ->with('owner');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id'); // 使用 user_id 字段进行模型关联
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        // Prepare notifications for all subscribers
        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each
            ->notify($reply);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function hasUpdatesFor($user)
    {
        // Look in the cache for the proper key
        // compare that carbon instance with the $thread->updated_at
        if (!$user)
        {
            return false;
        }

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    public function setSlugAttribute($value)
    {
        $slug = Str::slug($value);

        if (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-" . $this->id;
        }

        $this->attributes['slug'] = $slug;
    }

    public function incrementSlug($slug)
    {
        // 取出最大 id 话题的 Slug 值
        $max = static::whereTitle($this->title)->latest('id')->value('slug');

        // 如果最后一个字符为数字
        if(is_numeric($max[-1])) {
            // 正则匹配出末尾的数字，然后自增 1
            return preg_replace_callback('/(\d+)$/',function ($matches) {
                return $matches[1]+1;
            },$max);
        }

        // 否则后缀数字为 2
        return "{$slug}-2";
    }

    public function markBestReply(Reply $reply){
        $this->update(['best_reply_id' => $reply->id]);
    }

    public function lock()
    {
        $this->update(['locked' => true]);
    }

    //全局作用域
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update([
                'slug' => $thread->title,
                'body' => clean($thread->body,'thread_or_reply_body')
            ]);
        });
    }

}
