
{!! field()->text('title',
            __('catalog::dashboard.ages.form.title') ,
                    $model->title,
                  ['data-name' => 'title']
             ) !!}

{!! field()->checkBox('status', __('catalog::dashboard.ages.form.status')) !!}