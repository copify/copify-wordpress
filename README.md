![](https://raw.githubusercontent.com/copify/copify-wordpress/assets/banner-772x250.png)

## Copify Wordpress Plugin

[![Build Status](https://secure.travis-ci.org/copify/copify-wordpress.png?branch=master)](https://travis-ci.org/copify/copify-wordpress/)

Automatically publish unique, relevant blogs each week from Copify's team of professional writers. Now includes royalty free images.

### Requirements

* PHP >= 5.3
* WordPress >= 3.2.0
* PHP cURL extension
* PHP JSON library

### Installation

1. Unzip and upload the `copify` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu
3. Enter your API key which can be found on the settings page of your Copify account

### Automation

The plugin is best used alongside one of Copify's [monthly blog packages](http://copify.com/blog-packages). Enable the <b>auto-publish</b> setting to allow posts to go live immediately and we'll even add a Featured Image to the post!

### Workflow

We want to use git for version control, but WordPress uses SVN. Urgh.

So, clone/initialize the SVN repo locally using `git svn`:

```bash
$ git svn init --stdlayout https://plugins.svn.wordpress.org/copify
$ git svn fetch
```

Once setup, we use `--squash` to keep things in single commits. E.g. workflow;

```bash
git checkout -b bugfix
# new changes
git commit -a
git checkout master
git merge --squash bugfix
```

Then, we can use the `dcommit` option to sync back to WordPress's SVN repo.

```bash
$ git svn dcommit
```

### Assets

Set up a URL and svn branch for the assets

```bash
$ git config --add svn-remote.assets.url http://plugins.svn.wordpress.org/copify/assets
$ git config --add svn-remote.assets.fetch :refs/remotes/assets
```

Existing assets can then be fetched in to a new branch;

```bash
$ git svn fetch -r HEAD assets
$ git checkout -b assets
```

Make changes (edit icons or banners) then commit while on `assets` branch;

```bash
$ git commit -am "Edited icon"
$ git svn dcommit
```
