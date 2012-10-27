# About Hackathon Magento Honey Spam

This Magento Extension adds a field which is hidden with JavaScript to the Customer Register and the Product Review Form which looks like the URL Field of a Wordpress Comment Form.
If this field gets filled and the Form is send, there appears a error message and nothing will be safed.

There is also a check if this form is to fast transmitted, a human normally takes a couple of Seconds or more.

You can enable and disable the functionalitys or set the time how long it should take to transmit this forms in the Magento Admin Backend.


## Installation

Just copy the App folder to your Magento root directory and refresh your Magento Cache in the Shop Backend.


## Testing ##

After the Installation you should be able to find a input field with the id="url" in the Customer Register and in the Product Review Form, which should be hidden with JS.