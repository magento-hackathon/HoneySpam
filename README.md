# About Hackathon Magento Honey Spam

This Magento Extension adds a field which is hidden with JavaScript to the Customer Register and the Product Review Form which looks like the URL Field of a Wordpress Comment Form.
If this field gets filled and the Form is send, there appears a error message and nothing will be saved.

There is also a check if this form is too fast transmitted, a human normally takes a couple of Seconds or more.

You can enable and disable the functionalities or set the time how long it should take to transmit this forms in the Magento Admin Backend.

Freshly added: Regex-checking on all input-fields to determine a loose index for spam-level. It is not the most accurate thing at all, but it may prevent the low-end spam ("broadband spam"). You also can enable/disable this feature in the backend and set a maximum spam index trust level.

## Installation

### Compiler
Please disable Compiler. In our experience it doens't give performance wins and costs a lot of time while debugging.

### Installation with modman

Clone it within modman via `https://` or `git://`:

    modman clone https://github.com/magento-hackathon/HoneySpam.git
    modman clone git@github.com:magento-hackathon/HoneySpam.git

### Installation without modman

Copy all the files in your magento root directory

### After installation

Clean the cache after installation!

#### Contacts form modifications

Copy the file `app/design/frontend/{base_or_rwd}/default/template/contacts/form.phtml` to your own theme and insert the code

     <?php echo $this->getChildHtml('form_fields_before') ?>
     
within the `<form> ... </form>` tag.

#### Newsletter Subscription Form Modifications

Copy the file `app/design/frontend/{base_or_rwd}/default/template/newsletter/subscribe.phtml` to your own theme and insert the code

     <?php echo $this->getChildHtml('form_fields_before') ?>
     
within the `<form> ... </form>` tag.

## Testing

After the Installation you should be able to find a input field with the id="url" in the Customer Register and in the Product Review Form, which should be hidden with our JS.

## Further Information

### Core Contributors

- kortwotze
- Celldweller
- Andreas Emer
- Bastian Ike
- Fabian Blechschmidt

### Current Status of Project

Complete and working.

If you found errors or have ideas, [open an issue](https://github.com/magento-hackathon/HoneySpam/issues) on github or a pull request!
