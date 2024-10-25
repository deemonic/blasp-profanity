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
        $this->supportedLanguages = config('blasp.languages');

        if (empty($this->chosenLanguage)) {
            $this->chosenLanguage = config('blasp.default_language');
        }

        $this->validateChosenLanguage();

        $this->profanities = config("blasp_{$this->chosenLanguage}.profanities");
        $this->separators = config('blasp.separators');
        $this->substitutions = config('blasp.substitutions');
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
        $this->falsePositives = array_map('strtolower', config("blasp_{$this->chosenLanguage}.false_positives"));
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