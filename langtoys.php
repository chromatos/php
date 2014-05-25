<?php
# Hackish language toys
# License: WTFPL (See /copying)

# Add whatever keywords you want to be picked up on:

$keywords = array("crap", "poop", "beet", "radish", "monk", "robot", "cigarette", "donkey");

function isProperNoun($message)
{   return    strlen($message) > 1 && (($message[0] >= "A") && ($message[0] <= "Z")
           || strlen($message) > 2 && (($message[2] >= "A") && ($message[2] <= "Z"))
           || $message[1] == "'")
           ;
}

function isVowel($char)
{   $z = "eaoiuEAOIU";        # I'm just assuming it's slightly faster to do this
    if (strpos($z, $char[0])) # than to use stripos but I'm not going to test it.
        return true;
    else
        return false;
}

function isPlural($message)
# This obviously handles every possible plural:
{   $l = strLen($message) - 1;

#   Cheat for short words:
    if ($l < 4) return $message[$l] == "s";

    $has_s = $message[$l] == "s";
    if ($message[$l-0] == "s" && $message[$l-1] == "s")
        return false;

    if (isProperNoun($message))
        return $message[$l] == "'";

    if ($message[$l] == "s" && $message[$l-1] != "'")
        return true;

# I couldn't think of many words ending with "a" that weren't proper nouns or
# Latinate plurals so I put this in. It's not wrong; shut up!
    if ((substr($message, $l-1, $l) == "ii") || $message[$l] == "a")
        return true;

    return false;
}

function pluralize($message)
{   if (!isPlural($message))
    { $l = strlen($message) - 1;
        if (substr($message, $l-1, $l) == "um")
            return substr($message, 0, $l-1)."a";
        if (substr($message, $l, $l) == "y")
            return substr($message, 1, $l-1)."ies";
        return $message."s" ; # Is there a rule for "s" vs "es"? Yes, there is!
    }
    else
    return $message;
}

function pastify($message)
{   $l = strlen($message);
    if ($message[$l-1] == "p" && ($l > 2 && isVowel($message[$l-1]) && !isVowel($message[$l-2])))
        return $message."ped";
    if ($message[$l-1] == "n") return $message."ned"; # Yes, I'm aware
    if ($message[$l-1] == "e") return $message."d";
    else return $message."ed";
}

function hasWord($anArray, $aWord)
{   $z = 0;
    $high = count($anArray);
    while ($z < $high)
    {   if (  strToLower($anArray[$z]) == strToLower($aWord)
           || pluralize(strToLower($anArray[$z])) == strToLower($aWord)
           || pastify(strToLower($anArray[$z])) == strToLower($aWord)
           )
            return true;
        $z++;
    }
    return false;
}

function verbing($message)
{   $l = strlen($message);
    if ($message[$l-1] == "p" && ($l > 2 && isVowel($message[$l-1]) && !isVowel($message[$l-2])))
        return $message."ping";
    if ($message[$l-1] == "d" && ($l > 2 && isVowel($message[$l-1]) && !isVowel($message[$l-2])))
        return $message."ding";
    return $message."ing";
}

function isVerbing($message)
{   $l = strlen($message);
    return strtolower(substr($message, $l-3, $l-1)) == "ing";
}

function isPossesive($aWord)
{   return strlen($aWord) > 2 && ($aWord[strlen($aWord)-1] == "'" || $aWord[strlen($aWord)-2] == "'");
}

function pickoutKeyword($message)
# Nouns and verbs are the main words we want:
{   global $keywords;
    $wordles     = explode(" ", $message);
    $high        = count($wordles);
    $newKeywords = [];

    for($z = 0; $z < $high; $z++)
    {   if (  isProperNoun($wordles[$z])
           || hasWord($keywords, $wordles[$z])
           || (strlen($wordles[$z]) > 4 && substr($wordles[$z], strlen($wordles[$z])-3, strlen($wordles[$z])-1) == "ing")
           )  $newKeywords[count($newKeywords)] = $wordles[$z];

    }
    if (count($newKeywords) > 0)
	return $newKeywords[rand(0, count($newKeywords)-1)];
    else
	return null;

}


?>
