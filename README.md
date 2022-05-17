# VouchedFor

To run please use 

``` php index.php ```


## What I did

I created A class that would execute the functions. The idea would be to save the data to a database table If this was being implamented but that was avoided as there was a request for no extra frameworks/ libraries or tools and to not gold plate.

The Class will take in a new review. If the class is empty/first call then the first review will set the name varible within the class. 
There are gate clauses at the start of the function to stop un-needed processing if the review is not in the valid format or the reviewer has been de-activated.

Once the review has passed the checks it will be varified through 5 methods.
Each method will return the % gained or lossed within the varification step.

The first method checks if the review is Solicited. This is a very simple method that could have been done within the main code. But I chose to make it into its own method as the scope of the varification may change at a later date and this will mean that only the method will need to be changed.

The second method checks the word count. This like the first method is very simple but has been made into a method for the same reasons.

The third method checks the timestamp of the review and makes sure no other reviews came in at the same time or within the same hour.
I first convert the date to a timestamp so it is easier to manipulate. Then if the dates varible is empty this will be the first review so it will place the time into the array and return back to the main function.
If the dates array is not empty it will first check if any other reviews happon at the same time as this one. In the future if there are alot of reviews i went for a hash map sort of style so if the array index of the timestamp exists it then the code knows that there are other reviews that have come in within the same minute.
Next the date is checked to see if any reviews happoned within the same hour. As it is working with timestamps it is easy to see if another timestamp is within the same hour by checking if it is within 3600(1 hour) of the current timestamp.

The forth method checks the device. This works of hash mapping again where if the device is already registered it will return -30 and if not it will set the device for future checks.

The fifth method checks the average rating. If 5* then the return will be set to 2 and if the average of the class is less than 3.5 then that will be quadrupled.  
after the rating is checked it will update the average of the class and add 1 to the review count



I have used a basic array for the inputs as this is a test function that takes no user inputs and just shows the functionality of the code.

It then itterates through the reviews to demo getting a new review.
once this is done it will print the result to the command line