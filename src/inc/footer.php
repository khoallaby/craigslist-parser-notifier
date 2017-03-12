    <footer>
        <section><?php
            $timeStart = \Craigslist\WebUI::$timeStart;
            $time = microtime(true) - $timeStart;
            echo sprintf( 'Execution time: <b>%s</b> | %s posts found',
                $time,
                Craigslist\Database::getInstance()->getMysqliDb()->totalCount
            );
        ?></section>
    </footer>
</body>
</html>