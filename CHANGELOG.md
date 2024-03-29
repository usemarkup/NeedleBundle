## [2.1.2](https://github.com/usemarkup/NeedleBundle/compare/v2.1.1...v2.1.2) (2021-07-27)


### Bug Fixes

* **search:** apply boost fields with search term ([ad5ac16](https://github.com/usemarkup/NeedleBundle/commit/ad5ac16698942bc41d9de47272ac502979e7fe55))



## [2.1.1](https://github.com/usemarkup/NeedleBundle/compare/v2.1.0...v2.1.1) (2021-01-13)


### Bug Fixes

* **debug:** Removed internal constant present in new solr Symfony Debug Panel. Added optional configuration for the value. ([0962051](https://github.com/usemarkup/NeedleBundle/commit/0962051b8f70dce9313f09c4b05bad16f39b6c06))

# [2.1.0](https://github.com/usemarkup/NeedleBundle/compare/v2.0.0...v2.1.0) (2021-01-12)


### Bug Fixes

* **search:** typo bugfix after release 2.0.0 ([0b7161e](https://github.com/usemarkup/NeedleBundle/commit/0b7161ebfa36a1f8881634b399619ba21decf6f2))


### Features

* **debug:** new solr debug panel in symfony profiler ([0b59677](https://github.com/usemarkup/NeedleBundle/commit/0b59677cbb7a6f89d02d47cc7cb937a0919e5265))
* **standards:** removed CircleCI integration in favour of existing TravisCI integration ([dd62c05](https://github.com/usemarkup/NeedleBundle/commit/dd62c05252859c3745264f430fbd7fdd10dbd857))
* **search:** split range queries with multiple ranges to a union of ranges ([75b4910](https://github.com/usemarkup/NeedleBundle/commit/75b4910ff4e3af349e0ec86469e0611ad702c0c9))
* **standards:** updated the code to follow the coding standards and added circle.ci tests ([6426f14](https://github.com/usemarkup/NeedleBundle/commit/6426f146900be93f04d8b5082798ca2203113ec1))

# [2.0.0](https://github.com/usemarkup/NeedleBundle/compare/v1.2.0...v2.0.0) (2020-09-07)


### Features

* **search:** Adds function to define order for documents ([9795a9c](https://github.com/usemarkup/NeedleBundle/commit/9795a9cdb403cbc6abdbe57d9dde076808504a80))


### BREAKING CHANGES

* **search:** Removal of various public methods

# [1.2.0](https://github.com/usemarkup/NeedleBundle/compare/v1.1.0...v1.2.0) (2020-09-01)


### Features

* **query:** splits base, default and applied queries for better facet function ([1579159](https://github.com/usemarkup/NeedleBundle/commit/1579159ef0055ab4058d7a2084c2ae71fe659e28))

# [1.1.0](https://github.com/usemarkup/NeedleBundle/compare/v1.0.0...v1.1.0) (2020-08-28)


### Features

* increase timeout when doing a full index ([b79942d](https://github.com/usemarkup/NeedleBundle/commit/b79942da2770a2a673028a8731f8c59ba818feec))

# 1.0.0 (2020-08-21)


### Bug Fixes

* account for nonexistent corpus in indexing command ([82a326c](https://github.com/usemarkup/NeedleBundle/commit/82a326cbdf67314e57534939ab88a212efadfd33))
* Add delete queries to corpus indexing ([2c15f9a](https://github.com/usemarkup/NeedleBundle/commit/2c15f9a849ff21a036383301919335d8de81643d))
* add getRecord to ResolvedQueryInterface interface and implementation ([b8f36e0](https://github.com/usemarkup/NeedleBundle/commit/b8f36e06331b07760794eede3f09d25621f14bf2))
* add missing noop terms service registration ([06d868e](https://github.com/usemarkup/NeedleBundle/commit/06d868e7c7bd6b901ddd252cb72ce5c832884837))
* add missing sprintf call to correct exception creation ([202d240](https://github.com/usemarkup/NeedleBundle/commit/202d2406140f764d83bf0bc6426bec8595187bea))
* allow elasticsearch indexer to perform delete before full reindex ([6d24144](https://github.com/usemarkup/NeedleBundle/commit/6d24144b80f04fa9f309267d9df2de4e6eb1e5c8))
* by shared services i meant non-shared services - fix for symfony 2.8+ ([b4b053f](https://github.com/usemarkup/NeedleBundle/commit/b4b053feae3179ec535bdf9b7ce14f767d1f29ef))
* correct case where boolean value could be incorrectly set as null ([0f8f4e1](https://github.com/usemarkup/NeedleBundle/commit/0f8f4e1001923d27114715262283584bf7e58e8e))
* correct corpus indexing command test ([4b9ed83](https://github.com/usemarkup/NeedleBundle/commit/4b9ed837dfaa334d36c22ff5b6d18e7f4fbec7db))
* correct DateTime casing ([cf87361](https://github.com/usemarkup/NeedleBundle/commit/cf8736153d606a006185da6d4879578a0b9962c2))
* correct docblock for collection ([1d86592](https://github.com/usemarkup/NeedleBundle/commit/1d86592c0e659444dd71e5c8a0a58130a5030eb3))
* correct docblock/use statement in resolved query interface ([#29](https://github.com/usemarkup/NeedleBundle/issues/29)) ([293055c](https://github.com/usemarkup/NeedleBundle/commit/293055cab593558b5bd658d2c6b479254d9c2838))
* correct indexing command to correctly reflect whether full update is happening ([#55](https://github.com/usemarkup/NeedleBundle/issues/55)) ([67aea35](https://github.com/usemarkup/NeedleBundle/commit/67aea35ec3470637008026ae14ebb3db49dfced7))
* correct phpstan fails for 0.11.15 ([5f21ee0](https://github.com/usemarkup/NeedleBundle/commit/5f21ee00fad363bb1a43dc797ef63702690a4a35))
* correct return type for getContexts method in attr context registry interface ([b5bc00b](https://github.com/usemarkup/NeedleBundle/commit/b5bc00b8522d0b67c406c88ed901f6102f9fc4a1))
* correct some type issues/ class namings etc ([#47](https://github.com/usemarkup/NeedleBundle/issues/47)) ([ff50a07](https://github.com/usemarkup/NeedleBundle/commit/ff50a07c85681b40f81f03f441d66a8b6db7b423))
* corrected arguments in test for index scheduling event listener ([b63da30](https://github.com/usemarkup/NeedleBundle/commit/b63da3086c74004ffe8e01d85ae6668798649d20))
* Correctly set page values on PagerFanta results ([#60](https://github.com/usemarkup/NeedleBundle/issues/60)) ([b9d2b1d](https://github.com/usemarkup/NeedleBundle/commit/b9d2b1d22dc18e6cf0dd24f3f9e4e0070b976e70))
* **solarium:** corrects misue of explode (should be implode) ([de93918](https://github.com/usemarkup/NeedleBundle/commit/de93918f43f4da6146ff07d7a2ab6e5134225737))
* declare static dictionary provider class as abstract ([dec96c2](https://github.com/usemarkup/NeedleBundle/commit/dec96c29b6afcddd9ec15d84edc8c32f51a0ad02))
* dont filter against facet fields ([1b8586f](https://github.com/usemarkup/NeedleBundle/commit/1b8586fabe292860eff15509ecdf3ab29f28e2c0))
* ensure list of should arms of bool logic are generated correctly for elasticsearch ([4bdfa85](https://github.com/usemarkup/NeedleBundle/commit/4bdfa854515c54e8e03c089e3a3f31b5f5fe113c))
* ensure null result returns a traversable ([36b8731](https://github.com/usemarkup/NeedleBundle/commit/36b87316647c06f7b6e36836518ece745cfb5be6))
* ensure solarium plugins are attached to solarium clients ([64742db](https://github.com/usemarkup/NeedleBundle/commit/64742db0bb71d2dbf77ac5fb8310dde76f118946))
* **elastic:** escape special chars ([1a847bd](https://github.com/usemarkup/NeedleBundle/commit/1a847bd82b56b024227d50068dcff189348ee419))
* fix docblocks and adding missing instance variable ([06d2179](https://github.com/usemarkup/NeedleBundle/commit/06d217966a134861900330c068078b60c73b1ad6))
* **solr:** fix filter on filter key only ([c41eebe](https://github.com/usemarkup/NeedleBundle/commit/c41eebe14325d2b46db82142adf0fa0ced58722f))
* **test:** fixes bad test ([7e2c5f2](https://github.com/usemarkup/NeedleBundle/commit/7e2c5f2c83ae5cb2d4ca4c2c6cf539100df70cfb))
* handle NOT queries when using facet fields ([854f54b](https://github.com/usemarkup/NeedleBundle/commit/854f54b5c5ab33025c8c8ec6b3602d1871c60909))
* improve tests for php7 ([#31](https://github.com/usemarkup/NeedleBundle/issues/31)) ([c3ec0ae](https://github.com/usemarkup/NeedleBundle/commit/c3ec0ae43e0b892c0bc584eed9ed3b4bdeafe129))
* lower case managed resource keys ([d425629](https://github.com/usemarkup/NeedleBundle/commit/d425629fe1fffa2aeafed1ea0494c972e28cd073))
* perform flush on entity manager not an unknown variable ([cd9d9b9](https://github.com/usemarkup/NeedleBundle/commit/cd9d9b9e181e50cc276542e439f785280becd141))
* quote parameter for sf4 compat ([f83af0b](https://github.com/usemarkup/NeedleBundle/commit/f83af0b8181c756b2a2b44eb6194c796638702c7))
* remove referenced class from external codebase ([bf69372](https://github.com/usemarkup/NeedleBundle/commit/bf69372a97d1f1595124a1c4b0abd9180a47ed55))
* simplify decoration storage mechanism and allow for multiple calls (remove heap implementation) ([681d286](https://github.com/usemarkup/NeedleBundle/commit/681d286d8938f6a45753f0b60ded9c2b2aadf25a))
* stop pre-deleting by default in corpus indexing command ([#56](https://github.com/usemarkup/NeedleBundle/issues/56)) ([8dad4d0](https://github.com/usemarkup/NeedleBundle/commit/8dad4d094b1527390b9cef571e9999c9b2944978))
* typo in class name ([d9785f8](https://github.com/usemarkup/NeedleBundle/commit/d9785f8ddec263e3960b4d54662c27e19f2be2c7))
* use child definition dep inj class for Symfony 4 compatibility ([32df0c7](https://github.com/usemarkup/NeedleBundle/commit/32df0c74497ef717cb7d50486b2948563bb9b648))
* **solr:** uses plugin to deal with large query uri ([#26](https://github.com/usemarkup/NeedleBundle/issues/26)) ([a8711a2](https://github.com/usemarkup/NeedleBundle/commit/a8711a2a29fd7ec6630815489a9d07e33cebaef9))


### Features

* (breaking change) allow configuration of search context to specify use of fuzzy matching ([64f6bc0](https://github.com/usemarkup/NeedleBundle/commit/64f6bc0c73f23a3089c7bdbc3a9b07087c964ae2))
* abstract indexing process so command is not backend specific ([63221ab](https://github.com/usemarkup/NeedleBundle/commit/63221abac82baa5733815895d46f2bb02547c3c6))
* add an exception to allow easier debugging ([5cadf84](https://github.com/usemarkup/NeedleBundle/commit/5cadf84836ffbcacfe8879363207918843e3582e))
* add append option to allow just appending to a corpus ([73afc5f](https://github.com/usemarkup/NeedleBundle/commit/73afc5f69a178fe0e249a1965b5953cf23bbf168))
* add backend type provider service, and service locator for synonym clients ([0f5f966](https://github.com/usemarkup/NeedleBundle/commit/0f5f966c1a9cfadc0d8592dbabdb686ddfdfa097))
* **attributes:** add display name methods to specialization groups ([9d4c6c9](https://github.com/usemarkup/NeedleBundle/commit/9d4c6c984de9b42f627a215f65c640792c15780a))
* add elasticsearch implementation (exports, search without faceting) ([ac1242d](https://github.com/usemarkup/NeedleBundle/commit/ac1242de4c3fd51e10b866cf0634336a85635f45))
* **exceptions:** Add exception message ([#43](https://github.com/usemarkup/NeedleBundle/issues/43)) ([b22b793](https://github.com/usemarkup/NeedleBundle/commit/b22b793c9f9dcba6960ea25e7823c8e342f005ae))
* add explicit decorable search service interface ([94437cf](https://github.com/usemarkup/NeedleBundle/commit/94437cf349015834eb41efe8c8fe12f15c9866c5))
* add explicit exception during compilation when former backend type 'solarium' used ([32d2edd](https://github.com/usemarkup/NeedleBundle/commit/32d2eddd3736277b05dd1e2c0cdebfce1f135b2e))
* add fuzzy matching and search term normalization to solarium query builder (for solr) ([4a70009](https://github.com/usemarkup/NeedleBundle/commit/4a7000925e75a89b1203863bb21bfc088ea5e469))
* add fuzzy matching flag to resolved queries ([209dd3d](https://github.com/usemarkup/NeedleBundle/commit/209dd3d049350c9ed7732cbedcb633c30958b321))
* add generic export corpus command ([c3359ac](https://github.com/usemarkup/NeedleBundle/commit/c3359aca9e3887a19ebe49a94c6c35d4b2aff662))
* add markup coding stardard ([530a006](https://github.com/usemarkup/NeedleBundle/commit/530a0067f2b08ddc48be6aa43b724a728a21e66f))
* add shouldRequestFacetValueForMissing to SearchContextInterface ([84eb4a8](https://github.com/usemarkup/NeedleBundle/commit/84eb4a87714ed5199e315f02f76ba0c102873007))
* add shouldRequestFacetValueForMissing to SearchContextInterface ([5ad524c](https://github.com/usemarkup/NeedleBundle/commit/5ad524c3fd243d020b0b694f9443fdac5844341d))
* add SolrCoreAdminClient and reload core after add/delete managed resource ([0f42775](https://github.com/usemarkup/NeedleBundle/commit/0f42775918730a3fe866727f04e063eab308695c))
* add some basic faceting support for elasticsearch ([378a679](https://github.com/usemarkup/NeedleBundle/commit/378a679dc77db50fd3ff51769571423e9d888fe0))
* add static analysis at level 1 ([b1fbb0c](https://github.com/usemarkup/NeedleBundle/commit/b1fbb0cead601a57e9ba3570f25a697b92b0dcb6))
* add terms functionality ([6b068f0](https://github.com/usemarkup/NeedleBundle/commit/6b068f0ea1289190f2db099747bf8ce4dc8f75bb))
* **elastic:** add the ability to configure the elastic index ([4f7c447](https://github.com/usemarkup/NeedleBundle/commit/4f7c44793278434cbea9282e88cd24bb009f6a5b))
* add trait to help making recordable select queries ([30e80af](https://github.com/usemarkup/NeedleBundle/commit/30e80afc618f870ebfba28009f3f9d947079e687))
* Added GroupedResultPropertyIterator for iterating properties of grouped results ([7150b5e](https://github.com/usemarkup/NeedleBundle/commit/7150b5ec755f6e96db6b06a40ecd990493b31635))
* **attribute:** adds method to interface, adds tests ([a342080](https://github.com/usemarkup/NeedleBundle/commit/a3420804736fe19780008a1134d35e8388574d26))
* **attribute:** adds methods to get group internals ([9ebe39e](https://github.com/usemarkup/NeedleBundle/commit/9ebe39eb0d857dd6f39d78a4327a246db30f2433))
* **query:** adds query grouping via solarium/field collapsing ([a10def2](https://github.com/usemarkup/NeedleBundle/commit/a10def232a402e7cc2e2c8740bbb49fe3a6d6272))
* **attribute:** adds services for reading grouped specializations/contexts ([f7daf92](https://github.com/usemarkup/NeedleBundle/commit/f7daf9229bedeefcb7d7c5c957a4c7146badd2e3))
* **attribute:** adds specialized attribute decorators ([#39](https://github.com/usemarkup/NeedleBundle/issues/39)) ([e1f4ce7](https://github.com/usemarkup/NeedleBundle/commit/e1f4ce74512d0b7f2395938d2549325ed1e576db))
* adds unit tests for SolrCoreAdminClient ([660ac9b](https://github.com/usemarkup/NeedleBundle/commit/660ac9b19e16aa01a754c5b8611b2aa4fef621c7))
* allow a use wildcard search option against corpora ([66bb8a2](https://github.com/usemarkup/NeedleBundle/commit/66bb8a2cc19b7a2653d514588168cb26b373d4b6))
* allow adding per-subject callback to indexer ([69936eb](https://github.com/usemarkup/NeedleBundle/commit/69936ebdde5743d5d4692dd516581b456b95c91a))
* allow attributes in selected fields list in queries ([37af0fc](https://github.com/usemarkup/NeedleBundle/commit/37af0fc82e73740a0de38aa5984491f4d61eb60e))
* allow configuration of elasticsearch index prefix ([bba89cf](https://github.com/usemarkup/NeedleBundle/commit/bba89cffbd4237dc3591b966e553d88542373dfa))
* allow individual document deletes with elasticsearch ([0ff4bac](https://github.com/usemarkup/NeedleBundle/commit/0ff4bac590ccf64d53c881b0cb7ff63b74536869))
* allow registration of filters to validate combinations of specialized contexts ([a98d66b](https://github.com/usemarkup/NeedleBundle/commit/a98d66b87d5cd91ec93006e095f77199752db10d))
* allow shared services for all symfony versions ([#33](https://github.com/usemarkup/NeedleBundle/issues/33)) ([81f450a](https://github.com/usemarkup/NeedleBundle/commit/81f450ae616a7e7cfdd977d0ddd6fe49ed67578c))
* allow use of elasticsearch sdk v7 ([9fe5338](https://github.com/usemarkup/NeedleBundle/commit/9fe533816d9538b2d8dfe416f2a305187b859073))
* **guzzle:** allow using guzzle 6 ([#41](https://github.com/usemarkup/NeedleBundle/issues/41)) ([7178288](https://github.com/usemarkup/NeedleBundle/commit/7178288c3eed81b693c788ddbe65b69cbeebd32d))
* apply sorts when building elasticsearch query ([e718151](https://github.com/usemarkup/NeedleBundle/commit/e7181516476a6e2a74f39b8c4005d7e09710678e))
* **terms:** Apply wildcard modifiers to search suggest term ([#57](https://github.com/usemarkup/NeedleBundle/issues/57)) ([6bdb3e1](https://github.com/usemarkup/NeedleBundle/commit/6bdb3e1c292e1cf870b1081833538900020cb788))
* **monitoring:** change solr check to using zend diagnostics ([3220aeb](https://github.com/usemarkup/NeedleBundle/commit/3220aebca2c5ed61b97ad1afb0320171ce01604c))
* complete search service locator setup ([0b229a0](https://github.com/usemarkup/NeedleBundle/commit/0b229a06aeeae1b30731b8275585e13c20430e8f))
* create new search service locator for corpora, and split out solarium/solr config ([bf3c8f5](https://github.com/usemarkup/NeedleBundle/commit/bf3c8f56cbf7209e853c180ea0122501984b555e))
* declare a backend client service locator ([f59134b](https://github.com/usemarkup/NeedleBundle/commit/f59134be64424c2b0843822849c4c2a156b70eaa))
* drop pre symfony 3.4 support, and add support for symfony 4 ([75c443c](https://github.com/usemarkup/NeedleBundle/commit/75c443ccba124218544ef6e7bfd5a5ef35e6352a))
* drop support for liip monitor/ zend diagnostics checks ([da280de](https://github.com/usemarkup/NeedleBundle/commit/da280dec51b5a03b889c3bec730a448bdca3384c))
* **all:** Enforces passing runtime contexts ([5a71d9c](https://github.com/usemarkup/NeedleBundle/commit/5a71d9ca3d4bca7c75ab676b0cb9c85cbafb9cee))
* ensure facets get expected sorts ([c7a40c1](https://github.com/usemarkup/NeedleBundle/commit/c7a40c1ff3bce4caf49228ca6e93e02c6639f698))
* ensure filter search key prefers unparsed ([41af2a2](https://github.com/usemarkup/NeedleBundle/commit/41af2a26054920634cab94aa2053aa36081a21ec))
* ensure that facets under elastic take into account applied filters ([5aa7c53](https://github.com/usemarkup/NeedleBundle/commit/5aa7c53aeb43cf13039807b91d4446f348bb0a9d))
* expose original select query from resolved query ([3be4e0b](https://github.com/usemarkup/NeedleBundle/commit/3be4e0b03870a627c51e1d2d7b48e2d08289fc0d))
* implement a search term processor for lucene search terms, with normalization and fuzzy matching as options ([174ed73](https://github.com/usemarkup/NeedleBundle/commit/174ed731bfea45f02dd7dd91774e9dabd05c2448))
* implement elastic rendering of union, intersection and range queries ([eb2a983](https://github.com/usemarkup/NeedleBundle/commit/eb2a98360ba4490338a0b3416f7d9b16d6e04159))
* implement missing facet values for elasticsearch ([31e1392](https://github.com/usemarkup/NeedleBundle/commit/31e1392351ae37384853fe81c9c084542a14e18e))
* import synonym client definition into needle ([f3f9446](https://github.com/usemarkup/NeedleBundle/commit/f3f944626823095dc81c90f82fc0b1ab8ecebc7f))
* make elasticsearch export batch into 500s ([032fe18](https://github.com/usemarkup/NeedleBundle/commit/032fe18a2cecf5f7a9f13795a6ddf2fc049967e3))
* make explicit corpus indexing command factory type ([0f9fffb](https://github.com/usemarkup/NeedleBundle/commit/0f9fffbf09e274268bd7c2ff442130e124019ec9))
* make solarium dependencies optional ([649f795](https://github.com/usemarkup/NeedleBundle/commit/649f795e73ed81fff0f43068f736c2a911129a18))
* make solr search service async ([#58](https://github.com/usemarkup/NeedleBundle/issues/58)) ([4ff6318](https://github.com/usemarkup/NeedleBundle/commit/4ff6318be0c9b2c03f2becf741ceaff8d36d027b))
* make suggest service locator provide on a per corpus basis ([40d6342](https://github.com/usemarkup/NeedleBundle/commit/40d6342da8b64c6a03e00f47200ba21dbf36b07a))
* pass corpus into search services, and use for ES index ([36155bc](https://github.com/usemarkup/NeedleBundle/commit/36155bcac9a6da207e831c283a406c824d7a1728))
* rearrange bundle configuration so that multiple corpora with individual backends can be defined ([3d2464c](https://github.com/usemarkup/NeedleBundle/commit/3d2464c5fd8f22bf460b090b285f75a58304dd98))
* remove function to register indexing callbacks ([b946dcf](https://github.com/usemarkup/NeedleBundle/commit/b946dcf3827b617b3b795beac511339d2affd319))
* remove now-redundant needle service ([4bda5b6](https://github.com/usemarkup/NeedleBundle/commit/4bda5b6df4a372ba5d6ca3b7365237afb870eac4))
* remove standalone suggest service/ config option ([b695d62](https://github.com/usemarkup/NeedleBundle/commit/b695d62fa65c5e70c3b6516a83964ad7f7740736))
* remove temporary alias of synonym client interface to solr client ([17e93ba](https://github.com/usemarkup/NeedleBundle/commit/17e93ba61e102f3b2500c5aaade532fd6255f8fa))
* remove the potential for state to be held on fetches ([d64469e](https://github.com/usemarkup/NeedleBundle/commit/d64469ed12cd7f2a45ec45aaae2cf5a7b867638f))
* remove wrapping iterator mechanism from corpus indexer ([116dd8d](https://github.com/usemarkup/NeedleBundle/commit/116dd8d08daa44ab1ccffe1e0c5ecc2ad5d4b98b))
* rename solarium backend type as solr ([9a26e61](https://github.com/usemarkup/NeedleBundle/commit/9a26e6161ca484e419fe71a078fdc94e451ca90c))
* **elastic:** search for exact matches also ([0dd9c6c](https://github.com/usemarkup/NeedleBundle/commit/0dd9c6c1e0dfe87f4291f144deaedacb95f699f9))
* set up terms service locator ([21e7058](https://github.com/usemarkup/NeedleBundle/commit/21e7058802aa029bab25056427c32e6d8eb92e27))
* Solr managed resources clients ([762e42d](https://github.com/usemarkup/NeedleBundle/commit/762e42d72bfb75f18e8a8c30f2c91d0d77ad8ca9))
* specify rest_total_hits_as_int option in search queries for BC with es 6.x for now ([8eb7ef8](https://github.com/usemarkup/NeedleBundle/commit/8eb7ef8242ac73d57833e11f66b876999fd05b39))
* support having no facets in the result ([3c2244e](https://github.com/usemarkup/NeedleBundle/commit/3c2244efaefa32b69037018a730705c311bf60e8))
* support multiple attribute specializations ([#49](https://github.com/usemarkup/NeedleBundle/issues/49)) ([6490918](https://github.com/usemarkup/NeedleBundle/commit/649091889ed63983ec2df99e83aa5e0c91c052eb))
* **attribute:** throw exception on incorrect use of decorator ([#54](https://github.com/usemarkup/NeedleBundle/issues/54)) ([244bf42](https://github.com/usemarkup/NeedleBundle/commit/244bf422774551ea03345e90f5bd1c23d7c39611))
* **attributes:** throw more specific exceptions re missing context data, and handle better ([5a57cd0](https://github.com/usemarkup/NeedleBundle/commit/5a57cd07f525a6f14e7119a33be51cde24e5ba91))
* use boosts in ES queries ([4c756c4](https://github.com/usemarkup/NeedleBundle/commit/4c756c4288fc07b8898692711e28f44fb79b7cc9))
