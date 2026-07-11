import app from 'flarum/admin/app';
import Extend from 'flarum/common/extenders';

export default [
  new Extend.Admin()
    .setting(() => ({
      setting: 'signature.maximum_image_count',
      type: 'number',
      label: app.translator.trans('fof-signature.admin.settings.maximum_image_count.description'),
      help: app.translator.trans('fof-signature.admin.settings.maximum_image_count.help'),
    }))
    .setting(() => ({
      setting: 'signature.maximum_char_limit',
      type: 'number',
      label: app.translator.trans('fof-signature.admin.settings.maximum_char_limit.description'),
      help: app.translator.trans('fof-signature.admin.settings.maximum_char_limit.help'),
    }))
    .setting(() => ({
      setting: 'signature.allow_remote_images',
      type: 'boolean',
      label: app.translator.trans('fof-signature.admin.settings.allow_remote_images.description'),
      help: app.translator.trans('fof-signature.admin.settings.allow_remote_images.help'),
    }))
    .setting(() => ({
      setting: 'signature.allowed_image_hosts',
      type: 'textarea',
      label: app.translator.trans('fof-signature.admin.settings.allowed_image_hosts.description'),
      help: app.translator.trans('fof-signature.admin.settings.allowed_image_hosts.help'),
    }))
    .setting(() => ({
      setting: 'signature.allow_inline_editing',
      type: 'boolean',
      label: app.translator.trans('fof-signature.admin.settings.allow_inline_editing.description'),
      help: app.translator.trans('fof-signature.admin.settings.allow_inline_editing.help'),
    }))
    .setting(() => ({
      setting: 'signature.classic_look',
      type: 'switch',
      label: app.translator.trans('fof-signature.admin.settings.classic_look.description'),
      help: app.translator.trans('fof-signature.admin.settings.classic_look.help'),
    }))
    .permission(
      () => ({
        permission: 'moderateSignature',
        icon: 'fas fa-signature',
        label: app.translator.trans('fof-signature.admin.permissions.edit_signature_others'),
      }),
      'moderate'
    )
    .permission(
      () => ({
        permission: 'haveSignature',
        icon: 'fas fa-signature',
        label: app.translator.trans('fof-signature.admin.permissions.allow_signature'),
      }),
      'start'
    ),
];
