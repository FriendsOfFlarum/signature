import app from 'flarum/admin/app';

app.initializers.add('fof-signature', () => {
  app.extensionData
    .for('fof-signature')
    .registerSetting({
      setting: 'signature.maximum_image_count',
      type: 'number',
      label: app.translator.trans('fof-signature.admin.settings.maximum_image_count.description'),
      help: app.translator.trans('fof-signature.admin.settings.maximum_image_count.help'),
    })
    .registerSetting({
      setting: 'signature.maximum_char_limit',
      type: 'number',
      label: app.translator.trans('fof-signature.admin.settings.maximum_char_limit.description'),
      help: app.translator.trans('fof-signature.admin.settings.maximum_char_limit.help'),
    })
    .registerSetting({
      setting: 'signature.allow_inline_editing',
      type: 'boolean',
      label: app.translator.trans('fof-signature.admin.settings.allow_inline_editing.description'),
      help: app.translator.trans('fof-signature.admin.settings.allow_inline_editing.help'),
    })
    .registerPermission(
      {
        permission: 'moderateSignature',
        icon: 'fas fa-signature',
        label: app.translator.trans('fof-signature.admin.permissions.edit_signature_others'),
      },
      'moderate'
    )
    .registerPermission(
      {
        permission: 'haveSignature',
        icon: 'fas fa-signature',
        label: app.translator.trans('fof-signature.admin.permissions.allow_signature'),
      },
      'start'
    );
});
