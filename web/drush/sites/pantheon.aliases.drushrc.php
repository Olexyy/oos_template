<?php
  /**
   * Pantheon drush alias file, to be placed in your ~/.drush directory or the aliases
   * directory of your local Drush home. Once it's in place, clear drush cache:
   *
   * drush cc drush
   *
   * To see all your available aliases:
   *
   * drush sa
   *
   * See http://helpdesk.getpantheon.com/customer/portal/articles/411388 for details.
   */

  $aliases['dnz-135.dev'] = array(
    'uri' => 'dev-dnz-135.pantheonsite.io',
    'db-url' => 'mysql://pantheon:adeebd6ddb274daf9e892641a1828cbb@dbserver.dev.b917467f-118a-4ff3-82f7-7402f889a8fa.drush.in:14221/pantheon',
    'db-allows-remote' => TRUE,
    'remote-host' => 'appserver.dev.b917467f-118a-4ff3-82f7-7402f889a8fa.drush.in',
    'remote-user' => 'dev.b917467f-118a-4ff3-82f7-7402f889a8fa',
    'ssh-options' => '-p 2222 -o "AddressFamily inet"',
    'path-aliases' => array(
      '%files' => 'code/sites/default/files',
      '%drush-script' => 'drush',
     ),
  );
  $aliases['dnz-135.live'] = array(
    'uri' => 'live-dnz-135.pantheonsite.io',
    'db-url' => 'mysql://pantheon:5eb306d75c184f98a242c128888303cb@dbserver.live.b917467f-118a-4ff3-82f7-7402f889a8fa.drush.in:14528/pantheon',
    'db-allows-remote' => TRUE,
    'remote-host' => 'appserver.live.b917467f-118a-4ff3-82f7-7402f889a8fa.drush.in',
    'remote-user' => 'live.b917467f-118a-4ff3-82f7-7402f889a8fa',
    'ssh-options' => '-p 2222 -o "AddressFamily inet"',
    'path-aliases' => array(
      '%files' => 'code/sites/default/files',
      '%drush-script' => 'drush',
     ),
  );
  $aliases['dnz-135.test'] = array(
    'uri' => 'test-dnz-135.pantheonsite.io',
    'db-url' => 'mysql://pantheon:6c806c99d9144331a0ca8465007b66ac@dbserver.test.b917467f-118a-4ff3-82f7-7402f889a8fa.drush.in:14473/pantheon',
    'db-allows-remote' => TRUE,
    'remote-host' => 'appserver.test.b917467f-118a-4ff3-82f7-7402f889a8fa.drush.in',
    'remote-user' => 'test.b917467f-118a-4ff3-82f7-7402f889a8fa',
    'ssh-options' => '-p 2222 -o "AddressFamily inet"',
    'path-aliases' => array(
      '%files' => 'code/sites/default/files',
      '%drush-script' => 'drush',
     ),
  );
