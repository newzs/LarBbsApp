@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
@endsection

@section('content')
    <thread-view :thread="{{ $thread }}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    @include('threads._topic')


                    <replies @added="repliesCount++" @removed="repliesCount--"></replies>

                    {{--@foreach ($replies as $reply)--}}
                    {{--@include('threads.reply')--}}
                    {{--@endforeach--}}

                    {{--{{ $replies->links() }}--}}

{{--                    @if (auth()->check())--}}
{{--                        <form method="post" action="{{ $thread->path() . '/replies' }}">--}}

{{--                            {{ csrf_field() }}--}}

{{--                            <div class="form-group">--}}
{{--                                <textarea name="body" id="body" class="form-control" placeholder="说点什么吧..."rows="5"></textarea>--}}
{{--                            </div>--}}

{{--                            <button type="submit" class="btn btn-default">提交</button>--}}
{{--                        </form>--}}
{{--                    @else--}}
{{--                        <p class="text-center">请先<a href="{{ route('login') }}">登录</a>，然后再发表回复 </p>--}}
{{--                    @endif--}}
                </div>

                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p>
                                <a href="#">{{ $thread->creator->name }}</a> 发布于 {{ $thread->created_at->diffForHumans() }},
                                当前共有 <span v-text="repliesCount"></span> 个回复。
                            </p>
                            <p>
                                <subscribe-button :active="{{ json_encode($thread->isSubscribedTo)}}" v-if="signedIn"></subscribe-button>
{{--                                // 增加 Lock 按钮--}}
                                <button class="btn btn-default" v-if="authorize('isAdmin')" @click="toggleLock" v-text="locked ? 'Unlock' : 'Lock'">Lock</button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
