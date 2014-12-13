# How to contribute

*NB* All pull requests should be made to the `dev` branch of SeAT. Requests into `master` will _almost_ get auto closed. See *Making Changes*

If changes are needed due to vendor packages, please supply a link to the accompanying change log. Typically this would be due to Laravel and or dispatcher changes.

## Getting Started

* Make sure you have the latest `dev` version. Rebase your fork with with the origins `seat/dev` if needed. See [this link](http://stackoverflow.com/questions/3903817/pull-new-updates-from-original-github-repository-into-forked-github-repository) on how to keep your fork up to date.
* Submit your pull requests to `seat/dev` from the topic branch of your fork. Make sure you branch off the upstream `dev` branch for your fix/feature!
* Ensure that all your commits are descriptive enough to understand what they are either:
    * fixing
    * introducing as a new feature.
* One single commit per pull request is preferred.

## Making Changes

* Create a topic branch from where you want to base your work.
* Make commits of logical units.
* Check for unnecessary whitespace with `git diff --check` before committing.
* Make sure your commit messages are in the proper format. It needs to be descriptive. "Fix shit" means nothing!

## Submitting Changes

* Push your changes to a topic branch in your fork of the repository.
* Submit a pull request to the `seat/dev` repository on Github.

## So you made a mistake in your PR?
This happens more often then you would think, it could be a simple spelling mistake or a missing semicolon. Not to worry, the fix is quite simple

### Steps to follow are:
* Through the terminal reset your git history back by using the ```git reset HEAD~X``` command (where X is the number of commits you wish to jump back)
* Then apply the changes you wish to make to your files
* Re-add the files to your commit and resubmit.

The advantages of doing this are that it keeps the git history clean and makes it easier if a bug was ever introduced in a large PR then we can narrow down its search rather than having to go through several commits.

# Additional Resources

This section describes some details about how code should be styled. Styling is important to help keep the code in a clean, understandable and maintainable state.

## Comments.

Never. Not. Comment. You can hardly ever write too much comments. If you can, try to mimmic the Laravel indenting style of -3 characters per line for your comments. ie:

```php
// 123456789
// 123456
// 123
```

This is not a hard rule though.

## Indentation

The SeAT code base is indented with four (4) spaces. No tab indentation is allowed.

## Parenthesis

While a touchy subject, parenthesis usage rules are as follows.

### Functions / Classes
Functions/Classes have their parenthesis below the definition:

```php
Class Test extends Base
{

}
```

```php
public static function Testing
{

}
```

### Control Structures

Control structures such as `if`/`while`/`foreach` have their parenthesis on the same line as the structure definition:

```php
while(true) {

}
```

```php
foreach($this->thing as $other) {

}
```

**NOTE** If a control structure is followed by only one line, the parenthesis is omitted entirely. Multiline control structures have to to be enclosed in parenthesis.

```php
if($something !== true)
    break;
```

```php
while($we == 'not finished')
    print 'Hey!';

```

## General Naming Conventions

### Variables

In almost all cases, variables should be in the format:

```php
$var_name
```

`varName`, `VarName` etc are not acceptable.

### File Names

File names are generally in 2 formats. Either entirely lower case:

```php
file.php
```

Or in camel case:

```php
FileName.php
```

## General

- Files must end with one (1) newline.

Generally this depends on where in the application the file is, so try and follow a existing convention.

* #wcs-pub IRC channel on coldfront.net
