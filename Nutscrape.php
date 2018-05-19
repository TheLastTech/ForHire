<?php

if (file_exists("ids.json")) {
    $ids = json_decode(file_get_contents("ids.json"), true);
} else {
    $ids = array();
}
// Get a list of people to email
$ppltxt = file_get_contents("Emails.txt");
$ppl = explode("\n", $ppltxt);

foreach([ json_decode(file_get_contents("https://www.reddit.com/r/jobbit/new/.json"), true),
            json_decode(file_get_contents("https://www.reddit.com/r/forhire/new/.json"), true)] as $data )
if ($data) {


    $data2 = array_map("Pluck", $data['data']['children']);
    //optional array_filter("Filter",$data2); 
    foreach ($data2 as $job) {
        if (!array_key_exists($job['ID'], $ids)) {
            foreach ($ppl as $person) {
                $job['UserS'] = rawurlencode($job['User']);
                mail(trim($person), "New Job: ({$job['User']}: {$job['title']})", "http://reddit.com{$job['permalink']}\n\n\n{$job['Text']}\n\n\n\n-{$job['User']} https://www.reddit.com/message/compose/?to={$job['UserS']}");
            }
            $ids[$job['ID']] = 1;
        }
    }
    

}
file_put_contents("ids.json",json_encode( $ids));

function Pluck($node)
{
    $item = $node['data'];

    return array('created' => $item['created'],'User'=>$item['author'], "ID" => $item['name'], 'title' => $item['title'], "permalink" => $item['permalink'],'Text'=>$item['selftext']);

}

?>[]
