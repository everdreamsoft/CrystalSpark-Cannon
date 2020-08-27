# Grant Application: PolkadotAssetIndex DRAFT

## Project description

The objective of PolkadotAssetIndex is to deliver 

 1. `A Block Explorer for collectibles and tokenized assets (NFT's) substrate based blockchain such as Polkadot, Kusama.`


 2. `An API`

 3. `An Open Source PHP Library`








## Use case 

### For end users

Blockchain block explorers are very useful tools when working with a blockchain. 
But they are very hard to decrypt for non-technical users.

We are providing a comprehensive tool showing `the kitties you sent` or 
`your art collectible balance` for example
instead of a list of transaction hashes.


### For wallet providers and application developers

On smart contract based blockchain making a "getTokenBalance" on an  address is not trivial. In fact, it requires to query
every existing contract to have an exhaustive answer. It's even more complex on substrate based blockchain as not only multiple contract coexist 
but multiple pallets may have their own structure and logic.

PolkadotAssetIndex provide a way to get in one API call the balance.






## Architecture (work in progress)

### The Data Layer

### The Sandra Layer

Sandra Layer is our Open Source ontological database.

### The CSCannon Layer

### The PolkadotAssetIndex Layer






## Objectives
1. Token Abstraction: create an abstract class representing token management, regardless of the underlying structure.
2. Track and list collectible and art assets on most used substrate based chains
2. Create a modular structure allowing upcoming substrate pallet provider to easily integrate with PolkadotAssetIndex
3. Convert token balance toward federated asset balance
4. Convert token events into `asset` event
6. Ship library to allow developers to make their own token/asset index
7. Integrate our game items from our flagship game spells of genesis with PolkadotAssetIndex and issue game tokens on kusama/pokadot

## Technical challenges
### 1. Token and asset relationship

While managing a single token contract on a single chain is quite straightforward, it becomes more complex when there is a multiplicity of chains and standards.
Answering a simple question "what do I own" requires reconciling data from on-chain token and giving a meaning to those tokens.
For example a collectible asset `big lemon` can be represented by a token_id on a specific contract. Another token_id on another contract can represent a pack of 10 units of `big lemon`. 
Thus it's not possible to calculate the sum of owned `big lemon` by summing the token_id. The relationship between the asset and the token has to be defined in the code to be able to manipulate these concepts independently.


### 2. Multiple token definition ✓

On Ethereum and EVM based projects there are multiple token standards like ERC-20, ERC-721, ERC-1155.
Handling transfers or changing the state of these contracts require a different code implementation for each use case.
Each of these contracts uses a different method to get a user balance. PolkadotAssetIndex has to implement these differences 
while preserving a consistent abstract code interface such as `myAsset.send(TO_ADDRESS,QUANTITY)` or  
PolkadotAssetIndex is coming with a compatibility with the most used token interface while having a simple way to extend
 to new pallets or standards.




### 3. Language Unification ✓

We are building a comprehensive and consistent vocabulary to describe tokens and assets. 
For example, we are talking about `token contract` even though an NFT pallet doesn't support smart-contracts.
Unifying the concepts for tokenized asset allows a seamless transition between the protocols. 
Technically this requires the code to be abstract and flexible enough to take into account the multiplicity of data 



### Why PolkadotAssetIndex is good for the ecosystem




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
This project use extensively our ongoing development tools and libraries.
Sandra has MIT licence
CsCannon has GPLv3
All code built within the scope of the project will be eitheir integrated to CsCannon or released under 
GPLv3

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
45,000 USD

### Timeline and Milestones
This project will be executed in tree full months and can commence as soon as the grant application is approved.

#### Milestone 1: duration: 6 weeks

A PHP library allowing to create an index of balances, token and asset on any substrate based node.
The implemented and compatible pallets are usetech NFT and Moonbeam EVM with a compatibility with ERC-20
and ERC-721 token contracts. 

Payout: 15,000 USD

#### Milestone 2: duration: 4 weeks

THe library will implement specific queries 
holders

- Deliverables:
```
- Get holders of a specific token, an asset, or a collection
- Module library to easily implement new concrete classe for new pallets and or new token formats
```



Payout: 15,000 USD

#### Milestone 3: duration: 4 weeks

Project delivered in a form of 
```
- A web application in a form of a blockexplorer with a focus on collectible token
- an API returning assets and token balance on compatible listed token and  collections
- An open source PHP library allowing to make token and asset queries from distant datasources or build local database index
- We will issue collectible of our flagship game Spells of Genesis on a substrate base chain as a library reference implementation
```

- Deliverables: Unit tests

- Deliverables: Documentation and tutorials



Payout: 15,000 USD


