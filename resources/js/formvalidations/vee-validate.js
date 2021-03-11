import { extend, ValidationProvider } from 'vee-validate';
import { required, alpha, digits, numeric, max, min, email, image, size } from 'vee-validate/dist/rules'

extend('required', {
  ...required,
  message: 'This field is required'
});

extend('password', {
  params: ['target'],
  validate(value, { target }) {
    return value === target;
  },
  message: 'Make sure the passwords match'
});

extend('numeric', {
  ...numeric,
  message: 'This input must be a number'
})

extend('email', {
  ...email,
  message: 'This input must be a valid email address'
})

extend('image', {
  ...image,
  message: 'This input has to be an image file'
})

extend('size', {
  ...size,
  message: 'The maximum filesize is exceeded'
})

extend('alpha', {
  ...alpha,
  message: 'This input must only contain alphabetic characters'
})

extend('min', {
  validate(value, { length }) {
    return value.length >= length;
  },
  params: ['length'],
  message: 'The {_field_} field must have at least {length} characters'
});

extend('digits', {
  ...digits,
  message: 'This input must be a number with the specific lenght'
})

extend("decimal", {
  validate: (value, { decimals = '*', separator = '.' } = {}) => {
    if (value === null || value === undefined || value === '') {
      return {
        valid: false
      };
    }
    if (Number(decimals) === 0) {
      return {
        valid: /^-?\d*$/.test(value),
      };
    }
    const regexPart = decimals === '*' ? '+' : `{1,${decimals}}`;
    const regex = new RegExp(`^[-+]?\\d*(\\${separator}\\d${regexPart})?([eE]{1}[-]?\\d+)?$`);

    return {
      valid: regex.test(value),
    };
  },
  message: 'The {_field_} field must contain only decimal values'
})