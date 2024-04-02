## -

    - Locations [Main Prompt] (0)
    | + M'Which bus tickets do you want to buy?' 
    | + O'Kumasi'
    | + O'Accra'
    | + O'Takoradi'
    | + O'Cape Coast'
    | + O'Navrongo, etc..'
    | + I'Screen option [1,2,3,4,5,..]'
    |
     ------ - Number of Tickets [Level 1] (Stage 1)
       |    | + M'How many tickets do you want to purchase?'
       |    | + I'Generic input, validate as integer'
       |    |
       |     ------ - Payment Request Number [Level 1] (Stage 2)
       |       |    | + M'Enter the phone number for payment:'
       |       |    | + I'Generic input, validate its correct phone number'
       |       |    | 
       |       |     ------ - Confirmation of charge amount [Level 1] (Stage 3)
       |       |       |    | + M'You are about to pay GHs[amount] for [count] tickets, proceed?'
       |       |       |    | + O'Yes'
       |       |       |    | + O'No'
       |       |       |    | + I'Screen Option [1,2]'
       |       |       |    | 
       |       |       |     ------ - Thank you Prompt [Level 1] (Stage 4)
       |       |       |       |    | + M'Thank you for purchasing tickets with Arkebits' 
       |       |       |       |    | + I'No input, session end'
       |       |       |       |    | + A'initiate Payment prompt'



## -