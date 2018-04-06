<?php 
$current_page = Craigslist\WebUI::getCurrentPage(); 
?>
<header>
    <nav class="container">
        <ul class="list-inline mr-auto">
            <li class="<?php echo $current_page == '' ? 'active' : ''; ?>">
                <a class="nav-link " href="/"><i class="fa fa-home" aria-hidden="true"></i></a>
            </li>
            <li class="<?php echo $current_page == 'favorites' ? 'active' : ''; ?>">
                <a class="nav-link " href="/favorites"><i class="fa fa-heart" aria-hidden="true"></i></a>
            </li>
        </ul>
        <form class="form-inline">
            <input class="form-control mr-sm-2" type="text" placeholder="Search" ng-change="search(searchValue)"  ng-model="searchValue" ng-model-options="{debounce: 750}" />
            <!--<button class="btn btn-outline-primary" type="submit">Search</button>-->
        </form>
    </nav>
</header>
