# Langsel

Geeklog Language Selection Plugin (langsel) 

## Abstract
This plugin allows you to show a block or a dropdown menu(since v1.1.0) to select the UI language the user would like to use.  For registered users, the selection they have made will be stored in the user settings.

## Configuration
From the Configuration, you can choose how to display the language selector: Left blocks, Right blocks, and No block.
If you choose "No block", the language selector won't show as a block.  In this case, you can use a template variable {langsel} or an autotag \[langsel: language_names_separated by_space\].

## Examples

1. The following autotag \[langsel: english, japanese, french, german\] shows a language selector with four language options.
2. The following autotag \[langsel: auto\] shows a language selector with all options when the multi-language mode is disabled, or a language selector with given language options when the multi-language mode is enabled.

## Further Usage

You can directly call LANGSEL_buildSelector(array $options = array()) in "functions.inc."
  