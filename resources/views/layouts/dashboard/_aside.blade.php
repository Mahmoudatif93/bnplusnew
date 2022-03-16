<aside class="main-sidebar">

    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('uploads/user_images/' .auth()->user()->image) }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{auth()->user()->first_name.' '.auth()->user()->last_name}}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-th"></i><span>@lang('site.dashboard')</span></a></li>


            @if (auth()->user()->hasPermission('read_Companies'))
                <li><a href="{{ route('dashboard.Companies.index') }}"><i class="fa fa-th"></i><span>@lang('site.Companies')</span></a></li>
            @endif
            @if (auth()->user()->hasPermission('read_Cards'))
                <li><a href="{{ route('dashboard.Cards.index') }}"><i class="fa fa-th"></i><span>@lang('site.Cards')</span></a></li>
            @endif

           

            @if (auth()->user()->hasPermission('read_clients'))
            <li><a href="{{ route('dashboard.clients.index') }}"><i class="fa fa-th"></i><span>@lang('site.clients')</span></a></li>
            @endif

            @if (auth()->user()->hasPermission('read_orders'))
            <li><a href="{{ route('dashboard.orders.index') }}"><i class="fa fa-th"></i><span>@lang('site.orders')</span></a></li>
        @endif

        @if (auth()->user()->hasPermission('read_orders'))
            <li><a href="{{ route('dashboard.dubiorders.index') }}"><i class="fa fa-th"></i><span>@lang('site.dubiorders')</span></a></li>
        @endif
        @if (auth()->user()->hasPermission('read_orders'))
            <li><a href="{{ route('dashboard.dubioff.index') }}"><i class="fa fa-th"></i><span>@lang('site.dubioff')</span></a></li>
        @endif

        @if (auth()->user()->hasPermission('read_orders'))
            <li><a href="{{ route('dashboard.localcompany.index') }}"><i class="fa fa-th"></i><span>@lang('site.localcompany')</span></a></li>
        @endif
        @if (auth()->user()->hasPermission('read_orders'))
            <li><a href="{{ route('dashboard.nationalcompany.index') }}"><i class="fa fa-th"></i><span>@lang('site.nationalcompany')</span></a></li>
        @endif


        
        @if (auth()->user()->hasPermission('read_orders'))
            <li><a href="{{ route('dashboard.currancy.index') }}"><i class="fa fa-th"></i><span>@lang('site.currancy')</span></a></li>
        @endif

        <!--
        @if (auth()->user()->hasPermission('read_orders'))
            <li><a href="{{ route('dashboard.currancylocal.index') }}"><i class="fa fa-th"></i><span>@lang('site.currancyswagger')</span></a></li>
        @endif
-->
        

        @if (auth()->user()->hasPermission('read_orders'))
            <li><a href="{{ route('dashboard.swaggeroff.index') }}"><i class="fa fa-th"></i><span>@lang('site.swagger')</span></a></li>
        @endif



            @if (auth()->user()->hasPermission('read_users'))
            <li><a href="{{ route('dashboard.users.index') }}"><i class="fa fa-th"></i><span>@lang('site.users')</span></a></li>
            @endif







           {{-- @if (auth()->user()->hasPermission('read_users'))
                <li><a href="{{ route('dashboard.users.index') }}"><i class="fa fa-th"></i><span>@lang('site.users')</span></a></li>
            @endif--}}

            {{--<li><a href="{{ route('dashboard.Companies.index') }}"><i class="fa fa-book"></i><span>@lang('site.Companies')</span></a></li>--}}
            {{----}}
            {{----}}
            {{--<li><a href="{{ route('dashboard.users.index') }}"><i class="fa fa-users"></i><span>@lang('site.users')</span></a></li>--}}

            {{--<li class="treeview">--}}
            {{--<a href="#">--}}
            {{--<i class="fa fa-pie-chart"></i>--}}
            {{--<span>الخرائط</span>--}}
            {{--<span class="pull-right-container">--}}
            {{--<i class="fa fa-angle-left pull-right"></i>--}}
            {{--</span>--}}
            {{--</a>--}}
            {{--<ul class="treeview-menu">--}}
            {{--<li>--}}
            {{--<a href="../charts/chartjs.html"><i class="fa fa-circle-o"></i> ChartJS</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="../charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="../charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a>--}}
            {{--</li>--}}
            {{--<li>--}}
            {{--<a href="../charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a>--}}
            {{--</li>--}}
            {{--</ul>--}}
            {{--</li>--}}
        </ul>

    </section>

</aside>

