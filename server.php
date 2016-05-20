<?php
    header('Access-Control-Allow-Origin: *');
    
    if(isset($_GET['symbol'])) {  // stock quote
        $symbol = $_GET['symbol']; // url : http://localhost/hw8/server.php?&symbol=a
        
        $json_url = "http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=$symbol";
        $contents = file_get_contents($json_url); 
        $contents = utf8_encode($contents); 
        $json_result = json_decode($contents);

        $json_result->LastPrice = "$ ".number_format($json_result->LastPrice,2);
        
        $json_result->Change = number_format($json_result->Change,2);
        $json_result->ChangePercent = "( ".number_format($json_result->ChangePercent,2)."% )";
        
        date_default_timezone_set('America/Los_Angeles');
        $time = strtotime($json_result->Timestamp);
        $json_result->Timestamp = date("d F Y, h:i:s a T", $time);
        // $json_result->Timestamp = date("Y-m-d h:i A T", $time);
        
        if($json_result->MarketCap >= 1000000000) {
                $json_result->MarketCap = number_format($json_result->MarketCap/1000000000,2);
                $json_result->MarketCap = $json_result->MarketCap." Billion";
        }
        else if($json_result->MarketCap >= 1000000) {
                $json_result->MarketCap = number_format($json_result->MarketCap/1000000,2);
                $json_result->MarketCap = $json_result->MarketCap." Million";
        }

        // $json_result->Volume = number_format($json_result->Volume);
        // $json_result->ChangeYTD = number_format($json_result->LastPrice-$json_result->ChangeYTD,2);
        $json_result->ChangeYTD = number_format($json_result->ChangeYTD,2);
        $json_result->ChangePercentYTD = "( ".number_format($json_result->ChangePercentYTD,2)."% )";

        $json_result->High = "$ ".number_format($json_result->High,2);
        $json_result->Low = "$ ".number_format($json_result->Low,2);
        $json_result->Open = "$ ".number_format($json_result->Open,2);
            
        
        echo json_encode($json_result);
         
    }
    else if(isset($_GET['input'])) { // autocomplete
        $input = $_GET['input'];  // url : http://localhost/hw8/server.php?&input=a
        $json_url_autocomplete = "http://dev.markitondemand.com/Api/v2/Lookup/json?input=$input";
        $contents_autocomplete = file_get_contents($json_url_autocomplete); 
        $contents_autocomplete = utf8_encode($contents_autocomplete); 
        $json_result_autocomplete = json_decode($contents_autocomplete);

        echo json_encode($json_result_autocomplete);
    }
         
    else if(isset($_GET['news'])) 
    {
        $news = $_GET['news'];
        // Replace this value with your account key
        $accountKey = 'SZVr0XZvmpuSYvHNbraryHSipC6q22a6MqigIlE+tJE';      
        $ServiceRootURL =  'https://api.datamarket.azure.com/Bing/Search/';
                    
        $WebSearchURL = $ServiceRootURL . 'v1/News?$format=json&Query=';
                    
        $context = stream_context_create(array(
            'http' => array(
                'request_fulluri' => true,
                'header'  => "Authorization: Basic " . base64_encode($accountKey . ":" . $accountKey)
            )
        ));

        // $request = $WebSearchURL . urlencode( '\'' . $_GET['news'] . '\'');
        $request = 'https://api.datamarket.azure.com/Bing/Search/v1/News?Query=%27'.$news.'%27&$format=json';

        $response = file_get_contents($request, 0, $context);
        $response = utf8_encode($response);
        $json_result_news = json_decode($response);
        
        for($i=0; $i<count($json_result_news->d->results); $i++)
        {
            
            date_default_timezone_set('America/Los_Angeles');
            $time1 = strtotime($json_result_news->d->results[$i]->Date);
            $json_result_news->d->results[$i]->Date = date("d F Y H:i:s", $time1);

            // echo $json_result_news->d->results[$i]."<br>";
        }
                   
        echo json_encode($json_result_news);
    } 
             
/*
    else if(isset($_GET['news'])) { // news 
        $news = $_GET['news'];      // url : http://localhost/hw8/server.php?&news=aapl
        // $json_url_news = "https://ajax.googleapis.com/ajax/services/search/news?v=1.0&q=".$news."&userip=192.168.0.11";
        $json_url_news = "https://ajax.googleapis.com/ajax/services/search/news?v=1.0&q=".$news."&userip=76.168.126.80";
        $contents_news = file_get_contents($json_url_news); 
        $contents_news = utf8_encode($contents_news); 
        $json_result_news = json_decode($contents_news);

        echo json_encode($json_result_news);
    }
*/
    else if(isset($_GET['chart'])) { // chart
        $chart = $_GET['chart']; // url : http://localhost/hw8/server.php/?chart=aapl
 
        $json_url_chart = "http://dev.markitondemand.com/MODApis/Api/v2/InteractiveChart/json?parameters={\"Normalized\":false,\"NumberOfDays\":1095,\"DataPeriod\":\"Day\",\"Elements\":[{\"Symbol\":\"$chart\",\"Type\":\"price\",\"Params\":[%20\"ohlc\"]}]}";
        $contents_chart = file_get_contents($json_url_chart); 
        $contents_chart = utf8_encode($contents_chart); 
        $json_result_chart = json_decode($contents_chart);

        echo json_encode($json_result_chart);
    }
?>