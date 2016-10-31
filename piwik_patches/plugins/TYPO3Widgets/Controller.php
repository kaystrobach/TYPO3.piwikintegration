<?php

class Piwik_TYPO3Widgets_Controller extends Piwik_Controller
{
    public function rssAllNews()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }

    public function rssAllTeamNews()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/teams/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }

    public function rssCommunity()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/community/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }

    public function rssContentRenderingGroup()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/teams/content-rendering-group/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }

    public function rssDevelopment()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/development/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }

    public function rssExtensions()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/extensions/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }

    public function rssSecurity()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/teams/security/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }

    public function rssTypo3Org()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/teams/typo3org/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }

    public function rssTypo3Associaton()
    {
        $rss = new Piwik_ExampleRssWidget_Rss('http://news.typo3.org/news/typo3-association/rss.xml');
        $rss->showDescription(true);
        echo $rss->get();
    }
}
