![](https://raw.githubusercontent.com/copify/copify-wordpress/assets/banner-772x250.png)

## Copify Wordpress Plugin

[![Build Status](https://secure.travis-ci.org/copify/copify-wordpress.png?branch=master)](https://travis-ci.org/copify/copify-wordpress/)

Automatically publish unique, relevant blogs each week from Copify's team of professional writers. Now includes royalty free images.

### Requirements

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

Obviouslly we want to use git for version control, but WordPress uses SVN. Urgh.

We can get around this by using two local branches, `master` and `svn`.

When creating the `svn` branch locally, we set a different remote;

`git checkout -b svn remotes/git-svn`

Development can be carried out on any branch other than `svn` then when we want to publish out changes to WordPress, we merge using the `--squash` option;

`git checkout svn && git merge --squash myWorkingBranch && git svn dcommit`

This creates a single svn commit, which makes SVN play nice, and pushes the changes to the SVN server.