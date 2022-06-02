import React, { useState, useEffect } from 'react';
import logo from './logo.svg';
import axios from 'axios';
import './App.css';
import TextField from '@mui/material/TextField';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import Box from '@mui/material/Box';
import Container from '@mui/material/Container';
import Modal from '@mui/material/Modal';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Paper from '@mui/material/Paper';
import Typography from '@mui/material/Typography';
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
const API_URL = process.env.REACT_APP_API_URL || `https://newhiretest.dev.intuitsolutions-apps.net`
function stripTags(original) {
  return original.replace(/(<([^>]+)>)/gi, "");
}

function App() {
  const [loading, setLoading] = useState(false);
  const [visibleModal, setVisibleModal] = useState(false);
  const [messageModal, setMessageModal] = useState('Added Product to wishlist Successfully');
  const handleCloseModal = () => {
    setVisibleModal(false)
  }
  const [showMyWishlist, setShowMyWishlist] = useState(true);
  const [store, setStore] = useState({});
  const [searchResult, setSearchResult] = useState({});
  const [wishlist, setWishlist] = useState({});
  const [keyword, setKeyword] = useState('Microwave');
  useEffect(() => {
    async function fetchData() {
      let store = await getStoreInfo();
      console.log({ store });
      let my_wishlist = await getAllProductsInWishlist();
      setStore(store)
      setWishlist(my_wishlist)
    }
    fetchData();
  }, []);
  const showMyWishlistHandler = async (id) => {
    setShowMyWishlist(true)
  }
  const removeWishlist = async ({ id }) => {
    axios.post(`${API_URL}/remove-wishlist`, {
      id
    })
      .then(async (response) => {
        setVisibleModal(true)
        setMessageModal('removed successfully')
        let refresh_wishlist = await getAllProductsInWishlist();
        setWishlist(refresh_wishlist)
      })
      .catch(function (error) {
      });
  }
  const addToWishlist = async ({ id }) => {
    axios.post(`${API_URL}/add-to-wishlist`, {
      id
    })
      .then(async (response) => {
        setVisibleModal(true)
        setMessageModal('added to wishlish successfully')
        let refresh_wishlist = await getAllProductsInWishlist();
        setWishlist(refresh_wishlist)
      })
      .catch(function (error) {
      });
  }
  const getStoreInfo = async () => {
    const rawResponse = await fetch(`${API_URL}/store-info`);
    const store = await rawResponse.json();
    return store
  }
  const searchProduct = async (e) => {
    setShowMyWishlist(false)
    setLoading(true)
    setSearchResult({})
    const rawResponse = await fetch(`${API_URL}/search?keyword=${keyword}`);
    const response = await rawResponse.json();
    setSearchResult(response)
    setLoading(false)
  };
  const getAllProductsInWishlist = async () => {
    const rawResponse = await fetch(`${API_URL}/get-wishlist-products`);
    const response = await rawResponse.json();
    return response;
  }


  const styleModal = {
    position: 'absolute',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    width: 400,
    bgcolor: 'background.paper',
    border: '2px solid #000',
    boxShadow: 24,
    p: 4,
  };

  return (
    <div className="App">
      <Container>
        <section className='topbar'>
          store name : {store.name} <br />
          <button onClick={showMyWishlistHandler}>My Wishlist</button>
        </section>
        <header className="App-header">
          <Stack spacing={2} direction="row">
            <TextField
              defaultValue=""
              variant="filled"
              size="small"
              value={keyword}
              onInput={e => setKeyword(e.target.value)}
            />
            <Button variant="contained" onClick={searchProduct}>Search Products {loading ? '...' : ''}</Button>
          </Stack>
        </header>
        <section>
          {!showMyWishlist && (
            <div>
              {searchResult?.meta?.pagination?.count > 0 && (
                <TableContainer component={Paper}>
                  <Table sx={{ minWidth: 650 }} aria-label="simple table">
                    <TableHead>
                      <TableRow>
                        <TableCell width='80'>Product ID</TableCell>
                        <TableCell align="left">Title</TableCell>
                        <TableCell align="left">Description</TableCell>
                        <TableCell align="right">Price</TableCell>
                        <TableCell width='120' align="center">Action</TableCell>
                      </TableRow>
                    </TableHead>
                    <TableBody>
                      {searchResult?.data.map((row) => (
                        <TableRow
                          key={row.name}
                          sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
                        >
                          <TableCell align="left">{row.id}</TableCell>
                          <TableCell align="left">{row.name}</TableCell>
                          <TableCell align="left">{stripTags(row.description)?.substr(0, 36) + '...'}</TableCell>
                          <TableCell align="right">{'$ ' + row.price}</TableCell>
                          <TableCell align="center">
                            {wishlist?.data?.filter(p => p.id == row.id)?.length > 0 && (
                              <button className='btn' onClick={e => removeWishlist(row)}>
                               <DeleteForeverIcon></DeleteForeverIcon> 
                              </button>
                            )}
                            {(wishlist?.data?.length == 0 || wishlist?.data?.filter(p => p.id == row.id )?.length == 0) && (
                              <button className='btn' onClick={e => addToWishlist(row)}>
                                Add To Wishlist
                              </button>
                            )}
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </TableContainer>
              )}
              {searchResult?.meta?.pagination?.count == 0 && ('No product found')}
            </div>
          )}
          {showMyWishlist && (
            <div>
              <Typography variant="h6" component="h2">
                My Wishlist
              </Typography>
              {wishlist?.data?.length > 0 && (
                <TableContainer component={Paper}>
                  <Table sx={{ minWidth: 650 }} aria-label="simple table">
                    <TableHead>
                      <TableRow>
                        <TableCell width='80'>Product ID</TableCell>
                        <TableCell align="left">Title</TableCell>
                        <TableCell align="left">Description</TableCell>
                        <TableCell align="right">Price</TableCell>
                        <TableCell align="right"></TableCell>
                      </TableRow>
                    </TableHead>
                    <TableBody>
                      {wishlist?.data.map((row) => (
                        <TableRow
                          key={row.name}
                          sx={{ '&:last-child td, &:last-child th': { border: 0 } }}
                        >
                          <TableCell align="left">{row.id}</TableCell>
                          <TableCell align="left">{row.name}</TableCell>
                          <TableCell align="left">{stripTags(row.description)?.substr(0, 36) + '...'}</TableCell>
                          <TableCell align="right">{'$ ' + row.price}</TableCell>
                          <TableCell align="right">
                            <button className='btn' onClick={e => removeWishlist(row)}>
                              remove
                            </button>
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </TableContainer>
              )}
              {wishlist?.data?.length == 0 && ('No product in wishlist')}
            </div>
          )}
        </section>
        <Modal
          open={visibleModal}
          onClose={handleCloseModal}
          aria-labelledby="modal-modal-title"
          aria-describedby="modal-modal-description"
        >
          <Box sx={styleModal}>
            <Typography id="modal-modal-title" variant="h6" component="h2">
              {messageModal}
            </Typography>
          </Box>
        </Modal>
      </Container>
    </div>
  );
}
export default App;
