module.exports = function (config) {
  config.set({
    basePath: '../',

    files: [
        'app/lib/jquery.min.js',
        'test/api/**/*.js'
    ],

      proxies: {
          '/api/': 'http://127.0.0.1/backle/api/',
      },
      
    frameworks: ['jasmine'],

    autoWatch: true,

    browsers: ['Chrome'],

    junitReporter: {
      outputFile: 'test_out/unit.xml',
      suite: 'unit'
    }
  });
};
