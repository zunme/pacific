      <section class="home-area">
        <div class="container-sm text-center">
          <a href="{{ URL::route('home') }}" class="btn-top">홈</a>
          <span class="btn-top">{{$user->phone}}</span>
          <a href="{{ URL::route('logout') }}" class="btn-top">로그아웃</a>
        </div>
      </section>