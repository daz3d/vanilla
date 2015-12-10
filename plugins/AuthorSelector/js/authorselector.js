
// Enable multicomplete on selected inputs
function AuthorSelctorInit(id) {
    /// Author tag token input.
    var $author = $('#'.id);

    var author = $author.val();
    if (author && author.length) {
        author = author.split(",");
        for (i = 0; i < author.length; i++) {
            author[i] = { id: i, name: author[i] };
        }
    } else {
        author = [];
    }

    $author.tokenInput(gdn.url('/user/tagsearch'), {
        hintText: gdn.definition("TagHint", "Start to type..."),
        tokenValue: 'name',
        searchingText: '', // search text gives flickery ux, don't like
        searchDelay: 300,
        minChars: 3,
        maxLength: 25,
        prePopulate: author,
        animateDropdown: false,
        tokenLimit: 1
    });
}
