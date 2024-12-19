/**
 * Copyright © Qoliber. All rights reserved.
 *
 * @category    Qoliber
 * @package     Qoliber_ProductPack
 * @author      Jakub Winkler <jwinkler@qoliber.com>
 * @author      Wojciech M. Wnuk <wwnuk@qoliber.com>
 * @author      Łukasz Owczarczuk <lowczarczuk@qoliber.com>
 */
let config = {
    map: {
        '*': {
            productpack: 'Qoliber_ProductPack/js/productpack'
        }
    },
    shim: {
        productpack: {
            deps: ['jquery']
        }
    }
};
