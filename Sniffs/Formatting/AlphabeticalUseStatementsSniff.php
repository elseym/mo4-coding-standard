<?php

/**
 * This file is part of the mo4-coding-standard (phpcs standard)
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer-MO4
 * @author   Xaver Loppenstedt <xaver@loppenstedt.de>
 * @license  http://spdx.org/licenses/MIT MIT License
 * @version  GIT: master
 * @link     https://github.com/Mayflower/mo4-coding-standard
 */

/**
 * Alphabetical Use Statements sniff.
 *
 * Use statements must be in alphabetical order, grouped by empty lines
 *
 * @category  PHP
 * @package   PHP_CodeSniffer-MO4
 * @author    Xaver Loppenstedt <xaver@loppenstedt.de>
 * @copyright 2013 Xaver Loppenstedt, some rights reserved.
 * @license   http://spdx.org/licenses/MIT MIT License
 * @link      https://github.com/Mayflower/mo4-coding-standard
 */
class MO4_Sniffs_Formatting_AlphabeticalUseStatementsSniff
    extends PSR2_Sniffs_Namespaces_UseDeclarationSniff
{
    /**
     * last use statement seen in group
     *
     * @var string
     */
    private $_lastUseStatement = '';

    /**
     * line number of the last seen use statement
     *
     * @var int
     */
    private $_lastLine = -1;

    /**
     * current file
     *
     * @var string
     */
    private $_currentFile = null;

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        parent::process($phpcsFile, $stackPtr);

        if ($this->_currentFile !== $phpcsFile->getFilename()) {
            $this->_lastLine = -1;
            $this->_lastUseStatement = '';
            $this->_currentFile = $phpcsFile->getFilename();
        }

        $tokens = $phpcsFile->getTokens();
        $line   = $tokens[$stackPtr]['line'];
        $start  = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
        $end    = $phpcsFile->findNext([T_AS, T_SEMICOLON, T_COMMA], $stackPtr + 1);

        $currentUseStatement = $phpcsFile->getTokensAsString($start, $end - $start);

        if (($this->_lastLine + 1) < $line) {
            $this->_lastLine         = $line;
            $this->_lastUseStatement = $currentUseStatement;

            return;
        }

        if ($this->_lastUseStatement !== '') {
            if (strcmp($this->_lastUseStatement, $currentUseStatement) > 0) {
                $msg = 'USE statements must be sorted alphabetically';

                $phpcsFile->addError($msg, $stackPtr);
            }
        }

        $this->_lastUseStatement = $currentUseStatement;
        $this->_lastLine         = $line;
    }
}
 