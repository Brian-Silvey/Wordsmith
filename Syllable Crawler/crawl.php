<?php

include_once('simple_html_dom.php');
set_time_limit(0);

class crawl
{

  // create sleep timers (to prevent site from thinking this is a DOS attack)
  private $sleepMin;
  private $sleepMax;

  // create start location to begin crawling at
  private $startLetter;
  private $startPage;
  private $startWord;

  // create array to hold order of letters to loop through
  private $letters;

  // create html dom variable
  private $html;

  // create file variable (to write to)
  private $file;

  // initialize
  public function __construct($sleepMinStart = 0.14, $sleepMaxStart = 0.42, $passedStartLetter = 'u', $passedStartPage = 1, $passedStartWord = "")
  {
    global $sleepMin;
    global $sleepMax;
    global $startLetter;
    global $startPage;
    global $startWord;
    global $letters;
    global $html;
    global $file;

    $sleepMin = $sleepMinStart;
    $sleepMax = $sleepMaxStart;
    $startLetter = $passedStartLetter;
    $startPage = $passedStartPage;
    $startWord = $passedStartWord;
    
    // CHANGE TO YOUR PATH
    $letters = file("/Your/Path/To/letterlist.txt");

    // remove newline characters in each element
    for($x = 0; $x <= 25; $x++)
    {
      $letters[$x] = substr($letters[$x], 0, 1);
    }

    $html = new simple_html_dom();

    // CHANGE TO YOUR PATH
    $file = fopen("/Your/Path/To/syllable".$startLetter.".txt", "a+");
  }

  // close open references
  public function __destruct()
  {
    global $html;
    global $file;

    $html = null;
    if (is_resource($file))
      fclose($file);
  }

  // getters and setters
  public function setSleepMin($newSleepMin)
  {
    global $sleepMin;
    $sleepMin = $newSleepMin;
  }

  public function setSleepMax($newSleepMax)
  {
    global $sleepMax;
    $sleepMax = $newSleepMax;
  }

  public function getSleepMin()
  {
    global $sleepMin;
    return $sleepMin;
  }

  public function getSleepMax()
  {
    global $sleepMax;
    return $sleepMax;
  }

  // print letterlist.txt (used in testing to make sure that the file was put into an array that was properly readable)
  public function printLetters()
  {
    global $letters;
    foreach($letters as $letter)
    {
      echo $letter;
    }
  }

  // the beef of the class
  public function startCrawl()
  {
    global $html;
    global $file;
    global $startLetter;
    global $startPage;
    global $startWord;
    global $sleepMin;
    global $sleepMax;
    global $letters;

    // "data-syllable" is an attribute in the HTML, and the hyphen causes problems in parsing the PHP (which is remedied with a variable)
    $dataSyllable = 'data-syllable';
    
    // set default variable values
    $startIndex = 0;
    $gotoNext = false;
    $lastWord = "";
    $secondLastWord = "";

    // set $x to be the position of the start letter based on letterlist.txt
    for ($x = 0; $x <= 25; $x++)
    {
      if ($letters[$x] == $startLetter)
      {
        $startIndex = $x;
      }
    }

    $started = false;

    // loop through each browse page by letter
    for ($x = $startIndex; $x <= 26; $x++)
    {
      // if there is a next page, go to it
      if ($gotoNext == true)
      {
        // undo the $x++ from the loop that would normally change the letter
        $x--;

        // go to the next page, then sleep
        $targetUrl = "http://dictionary.reference.com/list/".$currLetter."/".$nextPage;
        $html->load_file($targetUrl);
        sleep((mt_rand($sleepMin*100, $sleepMax*100))/100);
        $nextPage++;
      }
      
      // else advance to the next letter
      else 
      {
        // if all pages have been visited on the last letter, end program
        if ($x == 26)
        {
          break;
        }

        // otherwise, reset the next page value, advance letters, and open the next letter file
        $nextPage = 2;
        $currLetter = $letters[$x];
        fclose($file);

        // CHANGE TO YOUR PATH
        $file = fopen("/Your/Path/To/syllable".$currLetter.".txt", "a+");

        // if just starting the program, override values to be based on passed in preferences
        if ($started == false)
        {
          $nextPage = $startPage + 1;
          $targetUrl = "http://dictionary.reference.com/list/".$currLetter."/".$startPage;

          // set $started so that this if statement won't be run again
          $started = true;
        }
      
        // else start on page 1 of current letter
        else
        {
          $targetUrl = "http://dictionary.reference.com/list/".$currLetter."/1";
        }

        // load the url for the current letter browse page and sleep
        $html->load_file($targetUrl);
        sleep((mt_rand($sleepMin*100, $sleepMax*100))/100);
      }

      // set default value of next page not existing
      $gotoNext = false;

      // loop through each link on the current page
      foreach($html->find('a') as $linkBrowse)
      {
        // if the NEXT button link is found and active, override $gotoNext value
        if (($linkBrowse->class) == "lnkactive")
        {
          if ($nextPage == substr($linkBrowse->href, -1))
          {
            $gotoNext = true;
          }
        }

        // only do something if the link leads to a list of words
        if (($linkBrowse->class) == "result_link")
        {
          // load the url for the page with the list of words
          $html->load_file($linkBrowse->href);
          sleep((mt_rand($sleepMin*100, $sleepMax*100))/100);

          // loop through each link on the current page
          foreach($html->find('a') as $linkList)
          {
            // only do something if the link leads to a specific word's page
            if (($linkList->class) == "result_link")
            {
              // if we have a startWord, check if current word is it
              if ($startWord !== "")
              {
                // if it is, then remove the startWord and begin saving info
                if (substr($linkList->href, 39) == $startWord)
                  $startWord = "";
                // else, go to next word and check if it's startWord
                else
                  continue;
              }

              // get the word from the URL
              $urlWord = substr($linkList->href, 39);

              // only accept single words without symbols
              if (strpos($word,'$') == false && strpos($urlWord,'-') == false && strpos($urlWord,'_') == false && strpos($urlWord,'.') == false && strpos($urlWord,'+') == false && strpos($urlWord,'!') == false && strpos($urlWord,'*') == false && strpos($urlWord,"'") == false && strpos($urlWord,"'") == false && strpos($urlWord,')') == false && strpos($urlWord,',') == false)
              {
                // load the word's page and sleep
                $html->load_file($linkList->href);
                sleep((mt_rand($sleepMin*100, $sleepMax*100))/100);

                // find each possible source of data on syllable count
                foreach($html->find('span') as $syllableCount)
                {
                  // if syllable data exists
                  if (isset($syllableCount->$dataSyllable))
                  {
                    // get the subject of the data
                    $subject = $syllableCount->$dataSyllable;

                    // only use the data if the subject is not a derived form
                    if (strpos($subject, 'ˌ') == false && strpos($subject, 'ˈ') == false)
                    {
                      // count number of syllable breaks, get the word, and write them into file in form "word:count"
                      $syllable = substr_count($subject, "·") + 1;
                      $word = str_replace("·", "", $subject);

                      // only accept single words without symbols (multiple words per 1 page exist)
                      if (strpos($word,'&') == false && strpos($word,'%') == false && strpos($word,'ʿ') == false && strpos($word,'$') == false && strpos($word,'-') == false && strpos($word,'_') == false && strpos($word,'.') == false && strpos($word,'+') == false && strpos($word,'!') == false && strpos($word,'*') == false && strpos($word,"'") == false && strpos($word,"'") == false && strpos($word,')') == false && strpos($word,',') == false && strpos($word,' ') == false)
                      {
                        // if there is a space at the end of the grabbed word, remove it
                        if (substr($word,-1) == " ")
                          $word = substr($word,0,strlen($word) - 1);

                        // if the word is different from the last two words, record it and update the last two words (this is to limit duplicates in real time)
                        if ($word !== $lastWord && $word !== $secondLastWord)
                        {
                          fwrite($file, $word.":".$syllable."\n");
                          $secondLastWord = $lastWord;
                          $lastWord = $word;
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    // cleanup
    fclose($file);
  }
}
?>
