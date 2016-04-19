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

We want to use git for version control, but WordPress uses SVN. Urgh.

So, initialise the SVN repo locally using `git svn`:

`git svn init --stdlayout https://plugins.svn.wordpress.org/copify`

Development can be carried out on any branch other than `svn` then when we want to publish our changes to WordPress, we merge using the `--squash` option;

`git checkout svn && git merge --squash myWorkingBranch`

The `svn` branch can now be commited with a single commit - important for SVN to play nice;

`git commit -a`

```bash
1 Squashed commit of the following:
2
3 commit bc314d4aec1ce5a69eaea06e601943f0cfe06eaf
4 Merge: b09a9b4 17a8ef7
5 Author: Rob McVey <robmcvey@gmail.com>
6 Date:   Wed Oct 8 13:24:53 2014 +0100

...
```

We can then publish using `dcommit`:

```bash
git svn dcommit
Committing to http://plugins.svn.wordpress.org/copify/trunk ...
	M	README.md
Committed r1003783
	M	README.md
r1003783 = 3a7bb6a653e0eba4a382c6bccdd95267aa3657b3 (refs/remotes/trunk)

...
```
