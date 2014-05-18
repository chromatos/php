<?php
# An exec script to rejoin as Mr. Mackey, M'kay?

#### jacking crutchy's exec script template:
# gpl2
# by chromas
# 17-may-2014

ini_set("display_errors","on"); # Too talkative; shows my php incompetence :-D

# alias|timeout|repeat|auto-privmsg|empty-trailing-allowed|php scripts/template.php %%trailing%% %%dest%% %%nick%% %%start%% %%alias%% %%cmd%% %%data%% %%exec%% %%params%%
$trailing=$argv[1];
$nick=$argv[2];
####

//$trailing = $argv[1]; # For local testing, disable above block and use this, M'kay?

function isProper($message)
{   if (($message[0] >= "A") && ($message[0] <= "Z")
      ||($message[2] >= "A") && ($message[2] <= "Z")
      || $message[1] == "'")
        return(true);
    else
        return(false);
}

function isVowel($char)
{   $z = "AEIOUaeiou";        # I'm just assuming it's slightly faster to do this
    if (strpos($z, $char[0])) # than to use stripos but I'm not going to test it.
        return(true);
    else
        return(false);
}

function isPlural($message)
# This obviously handles every possible plural:

{  // for ($z = 0; $z < strLen($message); $z++)
    $l = strLen($message) - 1;

    $has_s = $message[$l] == "s";
    if ($message[$l-0] == "s" && $message[$l-1] == "s")
        return(false);

    if (isProper($message))
        if ($message[$l] == "'")
            return(true);
        else
            return(false);

    if ($message[$l] == "s" && $message[$l-1] != "'")
        return(true);

#   I couldn't think of any words ending with "a" that weren't proper nouns or
#   Latinate plurals so I put this in. It's not wrong; shut up!
    if ((substr($message, $l-1, $l) == "ii") || $message[$l] == "a")
        return(true);

        return(false);
}

function pluralize($message)
{   if (!isPlural($message))
    {   $l = strlen($message) - 1;
        if (substr($message, $l-1, $l) == "um")
            return(substr($message, 0, $l-1)."a");
        if (substr($message, $l, $l) == "y")
            return(substr($message, 1, $l-1)."ies");
        return($message."s");
    }
    else
    return($message);
}

function prefix()
{   switch (rand(0,8))
    {   case 0: return("hmm, ");
        case 1: return("well, ");
        case 2: return("you see, ");
        case 4: return("you see, kids, ");
        case 5: return("now ");
        case 6: return("uh ");
        default: return "";
    }
}

function singularInfix()
{   switch (rand(0,3))
    { case 0: return("a ");
      case 1: return("the ");
      default: return "";
    }
}

function postfix()
{   switch (rand(0,6))
    {   case 0: return(".");
        case 1: return("?");
        case 2: return("!");
        default: return "";
    }
}

function sentence()
{   global $verb, $nick, $trailing;
    switch(rand(0, 6))
    { case 0 : return("$nick, quit fooling around and get back to your work, m'kay");
      default: return("$trailing$verb bad, m'kay");
    }
}

$low         = strtolower($trailing);
#$nick        = "2b"; # set by exec as the message sender
$theSentence = "";

if ((strpos($low,"mkay") === false) && (strpos($low, "m'kay") === false))
{   $theSentence .= prefix();

    if (rand(0,2) == 0)      # We'll change it sometimes for fun.
        pluralize($trailing);
    $x = isPlural($trailing);

    if ($x)
    {   $verb = " are";
    }
    else
    {   $verb = " is";
        $theSentence .= singularInfix();
    }
    $theSentence.=sentence().postfix();
    if (rand(0,1))
        $theSentence[0] = strtoupper($theSentence[0]);
    print $theSentence;
}
else
    print "m'kay";

print "\n";
?>
