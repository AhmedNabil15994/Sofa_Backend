<?php

namespace Modules\Setting\Repositories\Dashboard;

use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Modules\Core\Traits\Attachment\Attachment;
use Setting;

class SettingRepository
{
    use Attachment;

    function __construct(DotenvEditor $editor)
    {
        $this->editor = $editor;
    }

    public function set($request)
    {
        $this->saveSettings($request->except('_token', '_method'));

				return true;
    }

    public function saveSettings($request)
    {

        if (!auth()->user()->tocaan_perm){

            $newValue = Setting::get('other');
        
            if(isset($request['other']) && isset($request['other']['supported_payments'])){
    
                $newValue['supported_payments'] = $request['other']['supported_payments'];
            }else{
                unset($newValue['supported_payments']);
            }

            $request['other'] = $newValue;
        }
        
        foreach ($request as $key => $value) {

            if ($key == 'translate')
                  static::setTranslatableSettings($value);

            if ($key == 'images')
                  static::setImagesPath($value);

            if (auth()->user()->tocaan_perm && $key == 'env')
                  static::setEnv($value);

            Setting::set($key, $value);
        }
    }

    public static function setTranslatableSettings($settings = [])
    {
        foreach ($settings as $key => $value) {
            Setting::lang(locale())->set($key, $value);
        }
    }

    public static function setImagesPath($settings = [])
    {
        foreach ($settings as $key => $value) {
            $path = self::updateAttachment($value , config('setting.'.$key),'settings');
            Setting::set($key, $path);
        }
    }

    public static function setEnv($settings = [])
    {
        foreach ($settings as $key => $value) {
            $file = DotenvEditor::setKey($key, $value, '', false);
        }

        $file = DotenvEditor::save();
    }
}
