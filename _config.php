<?php
use SilverStripe\View\Parsers\ShortcodeParser;
use WebbuildersGroup\PackagistShortcode\PackagistShortCode;

define('PACKAGISTSHORTCODE_BASE', basename(dirname(__FILE__)));

//Enable the parser
ShortcodeParser::get_active()->register('packagist', array(PackagistShortCode::class, 'parse'));
