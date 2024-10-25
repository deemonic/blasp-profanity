<?php

namespace Blaspsoft\Blasp\Tests;

use Blaspsoft\Blasp\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('blasp_en.profanities', [
            'fucking',
            'shit',
            'cunt',
            'fuck',
            'penis',
            'cock',
            'twat',
            'ass',
            'dick',
            'sex',
            'butt',
            'arse',
            'lick',
            'anal',
            'clusterfuck',
            'bullshit',
            'fucked',
            'damn',
            'crap',
            'hell',
        ]);
        Config::set('blasp_en.false_positives', [
            'Scunthorpe',
            'Cockburn',
            'Penistone',
            'Lightwater',
            'Assume',
            'bass',
            'class',
            'Compass',
            'Pass',
            'Dickinson',
            'Middlesex',
            'Cockerel',
            'Butterscotch',
            'Blackcock',
            'Countryside',
            'Arsenal',
            'Flick',
            'Flicker',
            'Analyst',
            'blackCocktail',
        ]);
        Config::set('blasp_fr.profanities', [
            'putain',
            'connasse',
            'c0nn4ss3',
            'putain',
            'connard',
            'merdique',
            'bordel',
            'foutre',
            'Putain',
            'merde',
        ]);
        Config::set('blasp_fr.false_positives', [
            'passeur',
            'classe',
        ]);
        Config::set('blasp.languages', ['en', 'fr']);
        Config::set('blasp.separators', [' ', '-', '_']);
        Config::set('blasp.substitutions', [
            '/a/' => ['a', '4', '@', 'Á', 'á', 'À', 'Â', 'à', 'Â', 'â', 'Ä', 'ä', 'Ã', 'ã', 'Å', 'å', 'æ', 'Æ', 'α', 'Δ', 'Λ', 'λ'],
            '/b/' => ['b', '8', '\\', '3', 'ß', 'Β', 'β'],
            '/c/' => ['c', 'Ç', 'ç', 'ć', 'Ć', 'č', 'Č', '¢', '€', '<', '(', '{', '©'],
            '/d/' => ['d', '\\', ')', 'Þ', 'þ', 'Ð', 'ð'],
            '/e/' => ['e', '3', '€', 'È', 'è', 'É', 'é', 'Ê', 'ê', 'ë', 'Ë', 'ē', 'Ē', 'ė', 'Ė', 'ę', 'Ę', '∑'],
            '/f/' => ['f', 'ƒ'],
            '/g/' => ['g', '6', '9'],
            '/h/' => ['h', 'Η'],
            '/i/' => ['i', '!', '|', ']', '[', '1', '∫', 'Ì', 'Í', 'Î', 'Ï', 'ì', 'í', 'î', 'ï', 'ī', 'Ī', 'į', 'Į'],
            '/j/' => ['j'],
            '/k/' => ['k', 'Κ', 'κ'],
            '/l/' => ['l', '!', '|', ']', '[', '£', '∫', 'Ì', 'Í', 'Î', 'Ï', 'ł', 'Ł'],
            '/m/' => ['m'],
            '/n/' => ['n', 'η', 'Ν', 'Π', 'ñ', 'Ñ', 'ń', 'Ń'],
            '/o/' => ['o', '0', 'Ο', 'ο', 'Φ', '¤', '°', 'ø', 'ô', 'Ô', 'ö', 'Ö', 'ò', 'Ò', 'ó', 'Ó', 'œ', 'Œ', 'ø', 'Ø', 'ō', 'Ō', 'õ', 'Õ'],
            '/p/' => ['p', 'ρ', 'Ρ', '¶', 'þ'],
            '/q/' => ['q'],
            '/r/' => ['r', '®'],
            '/s/' => ['s', '5', '$', '§', 'ß', 'Ś', 'ś', 'Š', 'š'],
            '/t/' => ['t', 'Τ', 'τ'],
            '/u/' => ['u', 'υ', 'µ', 'û', 'ü', 'ù', 'ú', 'ū', 'Û', 'Ü', 'Ù', 'Ú', 'Ū'],
            '/v/' => ['v', 'υ', 'ν'],
            '/w/' => ['w', 'ω', 'ψ', 'Ψ'],
            '/x/' => ['x', 'Χ', 'χ'],
            '/y/' => ['y', '¥', 'γ', 'ÿ', 'ý', 'Ÿ', 'Ý'],
            '/z/' => ['z', 'Ζ', 'ž', 'Ž', 'ź', 'Ź', 'ż', 'Ż'],
        ]);
    }
}
