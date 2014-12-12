# Form builder package #
This package is meant to create complex forms. Based on commercial large scale applications, this package can be used to create complex forms and wizards.

### How to built a wizard ###
Use the registerStep method to define a step. Using a closure one can define what happens as a next step.

For instance:
```php
    $wizard = new Syn\Framework\Forms\Wizard;
    $user = new User;
    $wizard -> registerStep('user', new UserForm($user), function() use ($wizard)
    {
        if(Input::get('has-rights'))
            return 'right';
        else
            return 'summary';
    });
    $wizard -> registerStep('right', new RightForm($user), function() use ($wizard, $user)
    {
        if($user -> test)
            return 'form-index';
        else
            return 'summary';
    });
    $wizard -> registerSummary($user, [
        // use index and translatable string for label in summary
        'username' => ['user.username',1],
        // or simple strings
        'password' => 'password',
    ]);

    // the toString magic method will parse the wizard and the current form
    return (string) $wizard;
```