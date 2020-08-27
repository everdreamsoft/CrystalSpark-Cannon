# Grant Application: PolkadotAssetJS

## Project description
This project extends PolkadotJS with `asset tokenization` features. The objective is to simplify the workflow of issuing, querying and sending tokenized assets, commonly known in the blockchain world as NFTs. The deliverables of the project are a Typescript (Javascript) library for game and Dapp developers and a user-friendly reference implementation allowing artists and non-technical users 
to easily tokenize their visual arts to create collectibles.
As substrate is very flexible, PolkadotAssetJS is abstracting the concept of token across the multiple potential asset pallets and potential token contract format. This way the Dapp developer can interact with different contract types on different nodes, using a consistent workflow. The development time and complexity are reduced for content creators. It also makes the migration of Dapps and content from a parachain easier and the contract standard trivial.

PolkadotAssetJS is designed to be extendable to easily integrate new substrate assets pallets. In the scope of this project PolkadotAssetJS will be compatible with Usetech NFT pallet and Moonbeam EVM pallet. But the compatibility might be adapted to the most relevant pallets depending on the level of maturity and adoption in Polkadot.

## Use case 



### For developers

In less than 10 lines of code a developer can deploy a token contract, mint a token, assign metadata (the asset) and transfer it.

    let mySword = new Asset("Legendary Sword")
            .setAssetAttribute("power", 12)
            .setImageUrl()
            .setMetadataUrl(this)

        let blockchain = new Blockchain("wss://unique.usetech.com",NFT_PALLETE);

        let tokenContract = new TokenContract(blockchain,{name:'My Token',type:nftPallette.nonFungible});
        tokenContract.mint({quantity:1})
            .bindAsset(mySword)
            .transferTo(MY_ACCOUNT)
    }
    
When the developer wants to issue his asset on another chain or another pallet, he just needs to change one line:
    
    let blockchain = new Blockchain(A_SUBSTRATE_NODE, BY_DEFAULT_PALLETE);
The workflow remains unchanged.



### For end users

A reference GUI is implemented for end users as a form of a website. Users are able to easily see assets and collectible they own across all compatible Polkadot sidechains. The interface allows to easily mint and transfer tokens. Artists are able to upload their work, issue and bind
them to tokens without technical knowledge.

A substrate pallet creator or a node manager is able to easily deploy his own instance of the UI. 
The easy deployment allows end-users to quickly get started with node/pallet.




## Objectives
1. Token Abstraction: create an abstract class representing token management, regardless of the underlying structure.
2. Create a modular structure allowing upcoming substrate pallet provider to easily integrate with PolkadotAssetJS
3. Convert token balance toward federated asset balance
4. Convert token events into `asset` event
5. Create a user centric interface
6. Ship an NPM package useful for dapp developers.

## Technical challenges
### 1. Token and asset relationship ✓

While managing a single token contract on a single chain is quite straightforward, it becomes more complex when there is a multiplicity of chains and standards.
Answering a simple question "what do I own" requires reconciling data from on-chain token and giving a meaning to those tokens.
For example a collectible asset `big lemon` can be represented by a token_id on a specific contract. Another token_id on another contract can represent a pack of 10 units of `big lemon`. Thus it's not possible to calculate the sum of owned `big lemon` by summing the token_id. The relationship between the asset and the token has to be defined in the code to be able to manipulate these concepts independently.


### 2. Multiple token definition ✓

On Ethereum and EVM based projects there are multiple token standards like ERC-20, ERC-721, ERC-1155.
Handling transfers or changing the state of these contracts require a different code implementation for each use case.
Each of these contracts uses a different method to get a user balance. PolkadotAssetJS has to implement these differences while preserving a consistent abstract code interface such as `myAsset.send(TO_ADDRESS,QUANTITY)` or  
`myAddress.getAssetBalance()`.
PolkadotAssetJS is coming with a compatibility with the most used token interface while having a simple way to extend to new pallets or standards.




### 3. Language Unification ✓

We are building a comprehensive and consistent vocabulary to describe tokens and assets. 
For example, we are talking about `token contract` even though an NFT pallet doesn't support smart-contracts.
Unifying the concepts for tokenized asset allows a seamless transition between the protocols. 
Technically this requires the code to be abstract and flexible enough to take into account the multiplicity of data 



### Why PolkadotAssetJS is good for the ecosystem

Giving tools to content providers such as game developers, and by extension all content providers, will allow them to reduce their development time, ease their technological learning curve and accelerate end-users adoption.
PolkadotAssetJS is useful for Polkadot parachain builders, pallet creators and any substrate integrators.


### Why our team is interested
At EverdreamSoft, we believe that the “asset tokenization” is going to be the next big thing to revolutionize the game industry. 
We suppose that the growth in usage of tokenized asset will come mainly from the segment of non-professional creators, the “consume-creators” and that’s why we want to give them the power to leverage the asset tokenization revolution. 
We aim at developing tools to create, manage and share tokenized items that are simple to use, secure, transparent and compatible with multiple blockchains.

We believe that as many chains as the number of different governance beliefs will exist. We are building our tools to bring our content to serve users preference and not imposing a blockchain technology. 
Polkadot and substrate have built-in expandability. It makes an ideal ground to offer the power of decentralization and keeping the possibility to opt-in to different chains and governance systems,
while keeping consistency in ownership management.
 

## Organization & team
### Legal structure
EverdreamSoft SA (LTD) (Limited Liability Company)

Geneva Switzerland

### Execution
Work of this grant will be contracted and executed by the legal structure, its employees or its subcontractors under direct supervision of the leadership team.

### Open-source license
All artifacts which result from this grant will be open-sourced under the GPLv3 license.

### Leadership team
* Shaban Shaame
* Marketa Kortéova


### Team's experience

At EverdreamSoft, we have over 5-year experience in the field of blockchain for gaming and collectibles. 
Active since 2010 in game development, in 2015 we started exploring the possibilities of blockchain technology for the game industry. 
After a successful ICO, ran in August 2015 (one of the first crowdfunding campaigns of its kind), 
we launched the development of our flagship game Spells of Genesis (SoG), released in 2017. 
Today, the game is still available on app stores and is currently compatible with Counterparty and Ethereum.

 
In 2018, we initiated the development of Crystal Suite: an integrated suite of tools allowing you to explore, create, share and manage “Orbs” (rare digital items) linked to blockchain tokens, without unnecessary technicalities. 
Crystal Suite is composed of seven basic pillars that can be used as standalone products, or all together in order to reach a higher efficiency. The products designed for players, collectors, or digital art creators are Casa Tookan Wallet (released in Q3 2018), Orb Explorer (beta released in Q1 2020), Orb Creator and BitCrystals (blockchain currency, issued in 2015). 

Shaban Shaame, CEO and founder of the company, is also a founding member and treasurer of Blockchain Game Alliance.
https://blockchaingamealliance.org/





### Team websites
#### EverdreamSoft
Company website: https://www.everdreamsoft.com

#### Active ongoing projects (work in progress)
* Multi-chain  asset explorer (Counterparty ETH, Klaytn): https://www.orbexplorer.com
* Crystal Suite: https://crystalsuite.com

#### Main Game Product
* Spells of Genesis : www.spellsofgenesis.com

#### Collectible counterparty (bitcoin) wallet
* Book of Orbs : http://app.bookoforbs.com



### Team code repositories
* CSCannon (multichain tool library): https://github.com/everdreamsoft/CrystalSpark-Cannon
* Sandra: hhttps://github.com/everdreamsoft/sandra ontologic database system (general purpose but used for blockchain indexation)


### Team LinkedIn profiles
* Shaban: https://www.linkedin.com/in/shaban-shaame-83530b9/
* Markéta: https://www.linkedin.com/in/marketakorteova/




## Development Roadmap



## Funds Required Overall
40,000 USD

### Timeline and Milestones
This project will be executed in tree full months and can commence as soon as the grant application is approved.

#### Milestone 1: duration: 4 weeks
Basic library architecture a class diagram  delivered
-  1: `Blockchain` abstract class with the abstract logic
-  2: `Asset` concrete class and flexible metadata logic
-  3: `TokenPath` abstract class and abstract logic
-  4: `TokenContract` abstract class and abstract logic
-  5: `Pallete Package` architecture to easily implement palletes extending 

1. Deliverables: Architecture
The deliverable will allow to make the token management on a mock environment. 
The basic infrastructure of creating and binding asset is in place.

Payout: 10,000 USD

#### Milestone 2: duration: 4 weeks

Implementation of the library working on different palletes and connected to relevant running nodes.

All features adapted to at least two completely different pallets and on two contract standard ERC-20 
ERC-721.

- Deliverables:
```
- Library with abstract class and concrete classes for NFT and Moonbeam pallet
- Module library to easily implement new concrete classe for new pallets and or new token formats
```



Payout: 15,000 USD

#### Milestone 3: duration: 4 weeks

Project delivered in a form of 
```
- npm package PolkadotAssetJS as a github repository
- an HTML Typscript web application "Polkadot Asset Creator"
```

- Deliverables: Unit tests

- Deliverables: Documentation and tutorials



Payout: 15,000 USD


