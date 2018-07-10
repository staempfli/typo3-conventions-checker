# TYPO3 Conventions Checker

## What does it do?
This package lets you automatically test any changed code against the
[TYPO3 file formatting requirements][1] right before you commit your changes.
It also forces you to keep the commit messages tidy, according to the
[TYPO3 contribution workflow][2]. There are some checks we're
leaving out, though, as the commit message structure applies to
TYPO3 core contributions and we're not usually committing to the
core repository in our projects, eh.

This works via Git hooks, so every time you type 'git commit ...',
the configured GrumPHP tasks get fired.

The package itself doesn't do much, honestly. :) It provides some
configuration and requires some dependencies to get your
code quality and conventions checks set up in no time.

## But how does it do it?
Pretty simple. It requires [GrumPHP][3]
which hits up the [PHP Coding Standards Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
and well as the [PHP Mess Detector](https://github.com/phpmd/phpmd)
on each and every commit of yours. Only the files you're trying
to commit are getting checked for any coding standards violations
or code messes.

## Installing
### Prerequisites / TYPO3 project setup
This package expects you work with a composer-based TYPO3 setup, having a
(probably versioned) composer manifest outside your public html
directory called 'web' (though you may of course reconfigure
this directory's name). Something along the lines of:
```
- your-typo3-project/
  - bin
  - vendor
  (...)
  - composer.json
  - web
    - fileadmin
    - typo3
    - typo3conf
    (...)
```

- Reference the GrumPHP configuration file (`grumphp.yml`) in the
"extra" section of your root composer manifest:
```yaml
(...)
  "extra": {
    "grumphp": {
      "config-default-path": "vendor/staempfli/typo3-conventions-checker/conf/grumphp.yml"
    }
  }
```
(Alternatively, you can create a custom grumphp.yml file in your git root
directory, this will work as well).
- Require this package via composer `composer require --dev "staempfli/typo3-conventions-checker"`
- Commit changes to your project. All checks will be run automagically on commit.

## Options and Customizing
### Default configuration
Per default, this package uses
- a [php-cs-fixer](./conf/PhpCsFixer.php) configuration corresponding to the
[TYPO3 core php-cs-fixer configuration](https://github.com/TYPO3/TYPO3.CMS/blob/master/Build/.php_cs)
- Git commit message rules, mainly integrating the [TYPO3 core commit message format][2], excluding
  the ones specific to the TYPO3 core contribution workflow such as the
  'Releases' and/or 'Change-Id' requirements
- ([see full configuration in /conf/grumphp.yml](./conf/grumphp.yml))

### Override (i.e. 'extend') configuration
For this to work, you need to create your own grumphp.yml in your
git root directory according to the [GrumPHP][3] configuration. In there,
you may reference this package's grumphp configuration and override
the settings.

**Important notice on inheritance**: To my knowledge, it is not possible
to recursively inherit task settings, meaning: **you override one
property/setting of a task (see example below), you need to re-define all
tasks and their settings**. [See the grumphp configuration provided
for extensions](./conf/grumphp-extensions.yml).


#### Example 1: Reconfiguring the git commit message restrictions

Let's say you want to limit the git commit message length
to 42 characters. Update your
`grumphp.yml` from
```yaml
imports:
    - { resource: vendor/staempfli/typo3-conventions-checker/conf/grumphp.yml }
```
to something like
```yaml
imports:
    - { resource: vendor/staempfli/typo3-conventions-checker/conf/grumphp.yml }
parameters:
    tasks:                
        git_commit_message:
            max_subject_width: 42

        (other tasks' configuration with or without customization)
```


#### Example 2: Customizing the php-cs-fixer configuration
Like in the first example: reconfigure this via the grumphp
configuration. This time though, reference a different
(your own) php-cs-fixer configuration:
```yaml
imports:
    - { resource: vendor/staempfli/typo3-conventions-checker/conf/grumphp.yml }
parameters:
    tasks:
        phpcsfixer2:
            config: 'your/own/PhpCsFixer/conf.php'

        (other tasks' configuration with or without customization)
```

## Initializing extensions
Let's say, you maintain an extension 'mynews' and
of course, you want the code of 'mynews' to comply with some
coding standards as well. You're working on the main project,
where 'mynews' is included as a dependency but you'll probably
introduce changes to 'mynews' while working on the main project.
But, because 'mynews' is its own git repository, it doesn't get
checked by GrumPHP automatically if you just initialized GrumPHP on
your main project as described above.
```
- main-typo3-project/
  - composer.json
  - web
    - fileadmin
    - typo3
    - typo3conf
      - ext
        - mynews <= doesn't get initialized by GrumPHP
    (...)
```
Lucky you! You're just 2 baby steps away from forcing all
commits to 'mynews' to get checked by GrumPHP.

1. Add a reference to a grumphp.yml configuration via composer manifest of
the 'mynews' extension or create a custom grumphp.yml in the extension's
root directory. Note that this package already provides a
[dedicated grumphp configuration](./conf/grumphp-extensions.yml)
that respects the extension directory nesting.

Via 'extra' section of the composer manifest of the extension:
```json
  (...)
    "extra": {
        "grumphp": {
            "config-default-path": "./../../../../vendor/staempfli/typo3-conventions-checker/conf/grumphp-extensions.yml"
        }
    }
```
Via grumphp.yml in the root directory of the extension:
```yaml
imports:
    - { resource: ./../../../../vendor/staempfli/typo3-conventions-checker/conf/grumphp-extensions.yml }
```

2. In your root composer manifest, run the 'initializeExtensions'
method via composer scripts. Let's say, after each `composer update`, eh?
```json
(...)
"scripts": {
  "post-update-cmd": [
    "Staempfli\\Typo3ConventionsChecker\\Composer\\Grumphp::initializeExtensions",
  ],
},
(...)
```
<br />

#### Windows users
There currently is a bug, which leads to wrong paths of the grumphp
executable in the git hooks when used inside extensions as described
above. You might experience git cli responding with an error that the
GrumPHP executable could not be found. The reason for that is the
wrong registration in `.git/hooks/pre-commit` as well as
`.git/hooks/commit-message`.

**Solution**: In the files mentioned above, the calls to GrumPHP
might look something like this:
```
# Run GrumPHP
(cd "./" && printf "%s\n" "${DIFF}" | exec X:\path\to\your-site\web\vendor\bin\grumphp git:pre-commit '--skip-success-output')
```
Notice that the path to the GrumPHP executable is not in enclosed in quotes and
will lead to errors. Just enclose "X:\path (...) grumphp" in double
quotes and you're all set:
```
# Run GrumPHP
(cd "./" && printf "%s\n" "${DIFF}" | exec "X:\path\to\your-site\web\vendor\bin\grumphp" git:pre-commit '--skip-success-output')
```

[1]: https://docs.typo3.org/typo3cms/CodingGuidelinesReference/PhpFileFormatting/GeneralRequirementsForPhpFiles/Index.html
[2]: https://docs.typo3.org/typo3cms/ContributionWorkflowGuide/GitSetup/CommitMessageFormat.html
[3]: https://github.com/phpro/grumphp