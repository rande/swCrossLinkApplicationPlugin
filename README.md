# Cross Application Routing

This plugin allows to transparently used routes defined in other applications with no performance overhead or without loading applications' sfContext .

## Usage :

* From the frontend application, you can link back to the edit form 
    
        [php]
        <?php if($sf_user->isSuperAdmin()):?>
            <?php link_to('Edit Blog Post', '@backend.edit_post?id='.$blog->getId()) ?>
        <?php endif ?>
        
* Even better, if the edit_post route does not exists in the frontend, then the backend one is used 
    
        [php]
        <?php if($sf_user->isSuperAdmin()):?>
            <?php link_to('Edit Blog Post', '@edit_post?id='.$blog->getId()) ?>
        <?php endif ?>
    

## Installation

* Enable the feature, edit your app.yml

        [yml]
        all:
          swToolbox:
            cross_link_application:
              frontend:
                autoregister: true
                enabled: true                         # enable the feature
                load:
                  backend:
                    routes:                         # routes to load, leave empty will load all the routes
                      - homepage
                      - edit_blog
                    env:                            # define the environment
                      dev: rabaix.net/backend.php   # define the full path for the dev environment
                      prod: rabaix.net/backend      # define the full path for the prod environment

              backend:
                autoregister: true
                enabled: true
                load:
                  frontend:
                    routes:
                      - homepage
                      - edit_blog
                    env:
                      dev: rabaix.net/frontend_dev.php
                      prod: rabaix.net

* Edit your factories.yml

        [yml]
        all:
          routing:
            class: swPatternRouting

* In your frontend's template, you can access to a backend route like this

        [php]
        <?php link_to('Edit Blog Post', '@backend.edit_post?id='.$blog->getId()) ?>

* that's all !!

## Autoregister

The autoregister option load the route dynamically, this add a small overhead. You can speed up thing by setting the
autoregister to `false` and add routes definition in the `routing.yml` file.

        [yml]
        # encapsulated route
        backend.edit_blog:
          url: /blog/edit/:id
          class: swEncapsulateRoute
          options:
            encapsulated:
              name: backend.edit_blog


## Troubleshooting


### How to check that externals routes are loaded ? 

Routes from other applications are displayed in the log as : *Connect swEncapsulateRoute "app_name.route_name"*
If routes are not present then something is wrong in your settings.
    
### My settings look ok, but the routes do not appear

All routes loaded with an event or dynamically added at runtime cannot be used with this plugin. Routes have to be defined in the routing.yml of each application.
    
### symfony's bootstraping get slower after plugin installation

This might occurs if you load all routes from others application, it is a good practice to be explicit when you load externals routes : populate the *routes* section with routes you need in the current application.

