-- SQL script to update existing products to be weighted products
-- This updates products based on the weighted products list (codes 878-980)
-- Run this script carefully as it will modify existing product data

-- Update products to set weighted product fields
-- Format: UPDATE products SET ... WHERE product_name = 'name'

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000878',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සුදු කැකුලු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000879',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රතු කැකුලු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000880',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සුදු නාඩු හාල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000881',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රෝස හාල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000882',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කෑලි හාල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000883',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'තම්බපු කීරි සම්බ';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000884',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රට නාඩු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000885',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සම්බ සහල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000886',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රතු සම්බ';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000887',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සුදු සම්බ';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000888',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'තම්බපු සහල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000889',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'තම්බපු සම්බා';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000890',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'බාස්මතී';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000891',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ගමේ නාඩු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000892',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කැඩුනු සහල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000893',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කීරි සුදු කැකුලු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000894',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කීරි රතු කැකුලු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000895',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කීරි රතු සම්බ';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000896',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රතු සීනි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000897',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සුදු සීනි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000898',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'තේ කොල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000899',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පරිප්පු 1';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000900',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පරිප්පු 2';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000901',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කප් පරිප්පු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000902',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කඩල පරිප්පු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000903',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කඩල J';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000904',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කඩල M';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000905',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'මුං ඇට';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000906',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සුදු කව්පි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000907',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රතු කව්පි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000908',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කොල්ලු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000909',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ලංකා අල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000910',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රට අල .1';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000911',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රට අල .2';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000912',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'බී ළුෑණු රට';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000913',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'බී ළුෑණු ලංකා';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000914',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රතු ළුෑණු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000915',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සුදු ළුෑණු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000916',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පාන්පිටි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000917',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ඉදි ආප්ප පිටි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000918',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'උම්බලකඩ ලොකු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000919',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'උම්බලකඩ කෑලි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000920',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කුරක්කන් පිටි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000921',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ඉරිගු පිටි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000922',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'නූඩල්ස්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000923',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කොත්තමල්ලි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000924',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සූදුරු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000925',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'මාදුරු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000926',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කුරුදු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000927',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'එනසාල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000928',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ගොරකා';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000929',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ගොරකා කප්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000930',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'මිරිස් කරල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000931',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ඉගුරු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000932',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සියබලා';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000933',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'අමු කහ';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000934',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'මිරිස් කුඩු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000935',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'තුනපහ කුඩු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000936',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'බැදපු තුනපහ';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000937',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කෑලි මිරිස්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000938',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කහ කුඩු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000939',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ගම්මිරිස් ඇට';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000940',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ගම්මිරිස් කුඩු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000941',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සව් හාල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000942',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පපඩම්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000943',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'අබ';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000944',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'උළුහාල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000945',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ඊස්ට්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000946',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'බල කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000947',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'හුරුල්ලෝ කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000948',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කුකුලා කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000949',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'අංගුළු කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000950',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රට හාල්මැස්සො';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000951',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ලංක හාල්මැස්සො';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000952',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කලු කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000953',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පියමැස්සා';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000954',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කෙන්ද';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000955',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'බොම්බිලි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000956',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කූනිස්ස';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000957',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ඉස්සා';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000958',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ලින්නා බෝල්ලු';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000959',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'බෝල්ලු කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000960',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ලේනා පරව්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000961',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කෙලවල්ලා කූරි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000962',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කට්ට කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000963',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කීරමින් කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000964',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පලපු ලින්නා';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000965',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'වන්න පරව්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000966',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කොරලි කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000967',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'දුම් කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000968',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'මඩු කරවල';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000969',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'බෝට්ටු බලය';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000970',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'වියලි මිදි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000971',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'රට ඉදි';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000972',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'අජිනමොටෝ';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000973',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ආප්ප සෝඩා';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000974',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'සෝයා මීට්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000975',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පොල් තෙල් 1';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000976',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පොල් තෙල් 2';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000977',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'ෆාම් ඔයිල්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000978',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'පොල් තෙල් RBD';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000979',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'කුකුල් මස්';

UPDATE products SET
    is_weighted = 1,
    weighted_product_code = '000980',
    pricing_type = 'per_kg',
    allow_decimal_sales = 1,
    unit = 'kg',
    base_unit = 'g',
    conversion_factor = 1000
WHERE product_name = 'potatto';
