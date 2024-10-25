<?php

namespace Blaspsoft\Blasp;

use Exception;

abstract class BlaspExpressionService
{
    /**
     * Value used as a separator placeholder.
     *
     * @var string
     */
    const SEPARATOR_PLACEHOLDER = '{!!}';

    /**
     * Key to access profanities expressions.
     *
     * @var string
     */
    const KEY_PROFANITIES = 'profanities';

    /**
     * Key to access separators expressions.
     *
     * @var string
     */
    const KEY_SEPARATORS = 'separators';

    /**
     * Key to access substitutions expressions.
     *
     * @var string
     */
    const KEY_SUBSTITUTIONS = 'substitutions';

    /**
     * Key to access false positives expressions.
     *
     * @var string
     */
    const KEY_FALSE_POSITIVES = 'false_positives';

    /**
     * A list of possible character separators.
     *
     * @var array
     */
    private array $separators;

    /**
     * A list of possible character substitutions.
     *
     * @var array
     */
    private array $substitutions;

    /**
     * A list of profanities to check against.
     *
     * @var array
     */
    public array $profanities;

    /**
     * Escaped separator characters
     */
    private array $escapedSeparatorCharacters = [
        '\s',
    ];

    /**
     * An array containing all profanities, substitutions
     * and separator variants.
     *
     * @var array
     */
    protected array $profanityExpressions;

    /**
     * An array of separator expression profanities
     *
     * @var array
     */
    protected array|string $separatorExpression;

    /**
     * An array of character expression profanities
     *
     * @var array
     */
    protected array $characterExpressions;

    /**
     * Language the package should use
     *
     * @var string|null
     */
    protected ?string $chosenLanguage;

    /**
     * Languages supported by the package
     *
     * @var array
     */
    protected array $supportedLanguages;

    /**
     * An array of false positive expressions
     *
     * @var array
     */
    protected array $falsePositives;

    /**
     * Static cache for language-specific configurations
     *
     * @var array
     */
    protected array $cache = [];

    /**
     * @throws Exception
     */
    public function __construct(?string $language = null)
    {
        $this->chosenLanguage = $language;

        $this->loadConfiguration();

        $this->separatorExpression = $this->generateSeparatorExpression();

        $this->characterExpressions = $this->generateSubstitutionExpression();

        $this->generateProfanityExpressionArray();

        $this->generateFalsePositiveExpressionArray();
    }

    /**
     * Load Profanities, Separators and Substitutions
     * from config file.
     *
     * @throws Exception
     */
    private function loadConfiguration(): void
    {
        if (!isset($this->cache[$this->chosenLanguage])) {
            $this->supportedLanguages = config('blasp.languages');

            if (empty($this->chosenLanguage)) {
                $this->chosenLanguage = config('blasp.default_language');
            }

            $this->validateChosenLanguage();

            $this->cache[$this->chosenLanguage] = [
                self::KEY_PROFANITIES => config("blasp_{$this->chosenLanguage}.profanities"),
                self::KEY_SEPARATORS => config("blasp.separators"),
                self::KEY_SUBSTITUTIONS => config('blasp.substitutions'),
                self::KEY_FALSE_POSITIVES => array_map('strtolower', config("blasp_{$this->chosenLanguage}.false_positives")),
            ];

        }

        $this->profanities = $this->cache[$this->chosenLanguage]['profanities'];
        $this->separators = $this->cache[$this->chosenLanguage]['separators'];
        $this->substitutions = $this->cache[$this->chosenLanguage]['substitutions'];
        $this->falsePositives = $this->cache[$this->chosenLanguage]['false_positives'];

    }

    /**
     * @return string
     */
    private function generateSeparatorExpression(): string
    {
        return $this->generateEscapedExpression($this->separators, $this->escapedSeparatorCharacters);
    }

    /**
     * @return array
     */
    private function generateSubstitutionExpression(): array
    {
        $characterExpressions = [];

        foreach ($this->substitutions as $character => $substitutions) {

            $characterExpressions[$character] = $this->generateEscapedExpression($substitutions, [], '+') . self::SEPARATOR_PLACEHOLDER;
        }

        return $characterExpressions;
    }

    /**
     * @param array $characters
     * @param array $escapedCharacters
     * @param string $quantifier
     * @return string
     */
    private function generateEscapedExpression(array $characters = [], array $escapedCharacters = [], string $quantifier = '*?'): string
    {
        $regex = $escapedCharacters;

        foreach ($characters as $character) {
            $regex[] = preg_quote($character, '/');
        }

        return '[' . implode('', $regex) . ']' . $quantifier;
    }

    /**
     * Generate expressions foreach of the profanities
     * and order the array longest to shortest.
     *
     */
    private function generateProfanityExpressionArray(): void
    {
        $profanityCount = count($this->profanities);

        for ($i = 0; $i < $profanityCount; $i++) {

            $this->profanityExpressions[$this->profanities[$i]] = $this->generateProfanityExpression($this->profanities[$i]);
        }
    }

    /**
     * Generate a regex expression foreach profanity.
     *
     * @param $profanity
     * @return string
     */
    private function generateProfanityExpression($profanity): string
    {
        $expression = preg_replace(array_keys($this->characterExpressions), array_values($this->characterExpressions), $profanity);

        $expression = str_replace(self::SEPARATOR_PLACEHOLDER, $this->separatorExpression, $expression);

        $expression = '/' . $expression . '(?:s?)\b/i';

        return $expression;
    }

    /**
     * Generate an array of false positive expressions.
     *
     * @return void
     */
    private function generateFalsePositiveExpressionArray(): void
    {
        $this->falsePositives = array_map('strtolower', $this->falsePositives);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function validateChosenLanguage(): void
    {
        if (!in_array($this->chosenLanguage, $this->supportedLanguages, true)) {
            throw new Exception('Unsupported language.');
        }
    }
}