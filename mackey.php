<?php
# An exec script to rejoin as Mr. Mackey, M'kay?

#### jacking crutchy's exec script template:
# gpl2
# by chromas
# 17-may-2014

ini_set("display_errors","off"); # Too talkative; shows my php incompetence :-D

# alias|timeout|repeat|auto-privmsg|empty-trailing-allowed|php scripts/template.php %%trailing%% %%dest%% %%nick%% %%start%% %%alias%% %%cmd%% %%data%% %%exec%% %%params%%
$trailing = $argv[1];
$nick     = $argv[2];
####

include_once("langtoys.php");


function prefix()
{   switch (rand(0,8))
    { case 0 : return "hmm, ";
      case 1 : return "well, ";
      case 2 : return "you see, ";
      case 4 : return "you see, kids, ";
      case 5 : return "now ";
      case 6 : return "uh ";
      case 7 : return "now uh ";
      default: return "";
    }
}

function singularInfix()
{ switch (rand(0,3))
    { case 0 : return "a ";
      case 1 : return "the ";
      default: return "";
    }
}

function postfix()
{   switch (rand(0,6))
    { case 0 : return ".";
      case 1 : return "?";
      case 2 : return "!";
      default: return "";
    }
}

function sentence($aWord)
{   global $verb, $nick;
    switch(rand(0, 6))
    { # case 0 : return ""; # Add something
      default: return "$aWord$verb bad, m'kay";
    }
}

$low = strtolower($trailing);

$theSentence = "";

# I feel like I should throw in some booleans to store isProperNoun and others for optimization but zzzzz...

if ((strpos($low,"mkay") === false) && (strpos($low, "m'kay") === false))
{   if (rand(0,7))
    {   $theSentence .= prefix();
        $theWord      = pickoutKeyword($trailing);

        if ($theWord === null) exit;

        if (rand(0,2) == 0) # We'll change it sometimes for fun.
            pluralize($theWord);

        $x = isPlural($theWord);

        if ($x)
        { $verb = " are";
        }
        else
        {   if(rand(0,1))
                $verb = " is";
            else
                if (isPossesive($theWord) || isProperNoun($theWord))
                    $verb = " is";
                else
                    $verb = "'s";

            if (!isProperNoun($theWord) && !isVerbing($theWord)) $theSentence .= singularInfix();
        }
        $theSentence.=sentence($theWord);
    }
    else
    {   switch(rand(0,2))
        {   case 0 : $theSentence ="m'kay";
            #case 1 : $theSentence = ""; # TODO Insert some stuff
            default: $theSentence = "$nick, quit fooling around and get back to your work, m'kay";
        }
    }
}
else
    $theSentence = "m'kay";

if (rand(0,1))
$theSentence[0] = strtoupper($theSentence[0]);
print $theSentence;
print postfix()."\n";
?>
