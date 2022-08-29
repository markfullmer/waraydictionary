# Waray Dictionary and Part of Speech Tagger

This software includes a content management system and search interface for dictionary entries, including fields for up to 3 variations of meaning, part of speech, and sample sentence.

It also includes a part of speech tagging algorithm for the Waray language that utilizes morphology, syntax, and the dictionary-as-corpus for prediction.

### Methodology of the Waray Part of Speech Algorithm

This algorithm is based on principles outlined by Voltaire Oyzon in "A Corpus-based study of the morphosyntactic functions of Waray substantive lexical items" (2020). It uses a dictionary of known syntax (location in clause) and morphology (prefix, suffix) patterns in the Waray language to evaluate 23 rules. It then applies a scoring system to estimate the probability of predicate (verb), referential (noun), or modificative (adjective) of the target word.

Common modifiers (e.g., "la," "pa,", "gad", "ngay-an") are often inserted between substantive words that would indicate part of speech. Therefore, the algorithm ignores these when evaluating syntax. For example, it will parse "gin-aanak pa la hiya" as "gin-aanak hiya," and can identify that a pronoun ("hiya") is following the word "gin-aanak".

For similar reasons, clausal beginnings ("kun", "kay", "ano") are ignored. For example, "Kun ano kadakó an butones sugad man an kadákó han ohales" will consider "kadakó" the beginning of the clause for the purposes of identifying part of speech.

The part of speech of adjacent words often indicates a word's likely part of speech. For example, a clause is less likely to have a predicate adjacent to another predicate, rathern than adjacent to a modifier or referential. The algorithm therefore evaluates the part of speech of adjacent words to predict the target word's part of speech. It achieves this in two ways: first, it checks the Waray dictionary for the adjacent word's part of speech; if the word's part of speech is not found in the dictionary, it then applies its own part-of-speech guessing algorithm to the adjacent word (the algorithm uses itself to improve its guess for the target word!). If it can establish a high probability of that word's part of speech, it then can apply 'adjacency' rules to the target word.

At present, this algorithm does not evaluate infixes, which are a common feature of Filipino languages (e.g., "palit" [buy] becomes "pumalit" [bought] with the infix "um"). In order for the algorithm to evaluate infixes, it would need a dictionary of Waray word roots. This effort is underway.

A future planned enhancement is to add corpus similarity comparison to the algorithm: if a sample sentence can be found in the corpus that demonstrates sufficient similarlity (e.g., the adjacent words in the sample sentence are the same as in the evaluated sentence), the dictionary's part of speech can be calculated into the probability.

A caveat about part of speech tagging algorithms: they cannot be 100% accurate. To give just one example from English, in the sentence "Working late into the night is draining," the word "working" functions as a referential. However, if the same clause is located in "Working late into the night, Mark was drained," now "working" functions as a predicative.
