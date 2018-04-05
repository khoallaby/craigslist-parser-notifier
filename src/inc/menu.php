<?php 
$current_page = Craigslist\WebUI::getCurrentPage(); 
?>
<header>
    <nav class="container">
        <ul class="list-inline mr-auto">
            <li class="<?php echo $current_page == '' ? 'active' : ''; ?>">
                <a class="nav-link" href="/">Home</a>
            </li>
            <li class="<?php echo $current_page == 'favorites' ? 'active' : ''; ?>">
                <a class="nav-link" href="/favorites">Favorites</a>
            </li>
        </ul>
        <form class="form-inline">
            <input class="form-control mr-sm-2" type="text" placeholder="Search">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
    </nav>
</header>
