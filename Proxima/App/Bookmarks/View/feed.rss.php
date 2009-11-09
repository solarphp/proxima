<?php
// output the header this way so as not to let the XML
// tags interfere with PHP
echo '<?xml version="1.0" encoding="iso-8859-1" ?>' . "\n";

// build the item list.  we do this here so we can catch
// the latest date in the list, and make the channel reflect
// that date.
$items = '';
$latest = ''; // last update
foreach ($this->list as $item) {
    
    $tags        = str_replace(' ', '+', $item->tags_as_string);
    $category    = $this->escape($item->member_handle . ': ' . $tags);
    $title       = $this->escape($item->subj);
    $pub_date    = $this->date($item->updated, DATE_RSS);
    $description = $this->escape($item->summ);
    $link        = $this->escape($item->uri);
    
    if ($item->updated > $latest) {
        $latest = $item->updated;
    }
    
    $items .= <<<ITEM
        
        <item>
            <category>$category</category>
            <title>$title</title>
            <pubDate>$pub_date</pubDate>
            <description>$description</description>
            <link>$link</link>
        </item>
        
ITEM;
}

// strip the format from the current URI so we get the source page
$uri = $this->actionUri();
$uri->format = '';


?>
<rss version="2.0">
    <channel>
        <title><?php echo $this->escape($this->layout_title) ?></title>
        <link><?php echo $this->escape($uri->get(true)) ?></link>
        <description><?php echo $this->escape($this->feed['descr']) ?></description>
        <pubDate><?php echo $this->date($latest, DATE_RSS) ?></pubDate>
        <?php echo $items ?>
    
    </channel>
</rss>