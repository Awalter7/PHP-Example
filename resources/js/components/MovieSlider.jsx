import React, { useEffect, useState } from 'react'
import { motion, AnimatePresence, LayoutGroup } from 'framer-motion'
import { FaCheckCircle } from "react-icons/fa";
import { TbTrashXFilled } from "react-icons/tb";

export default function MovieSlider({ movies, code, currentName, sessionId }) {
    const [m, setM] = useState(movies);
    const [midIndex, setMidIndex] = useState(0);
    const [liked, setLiked] = useState([])
    const [likedIds, setLikedIds] = useState([])
    const [dislikedIds, setDislikedIds] = useState([])
    const [commonLiked, setCommonLiked] = useState([])



    function handleApproval(movie, approved) {
        if (!movie?.id || likedIds.includes(movie.id)) return;

        // optimistically update UI
        console.log(approved)
        if(approved){
            setLikedIds(ids => [...ids, movie.id]);
            setLiked(list => [...list, movie]);
        }else{
            setDislikedIds(list => [...list, movie.id])
        }

        // tell server to record it
        fetch(`/session/${code}/update-approval`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',            // ← ensure JSON response
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                movie_id: movie.id,
                approve: approved 
            })
        })
        .then(r => r.json())
        .then(data => {
            // you could sync `approved` back into your UI if you want
            console.log('Server-side approved list:', data.approved);
        })
        .catch(() => {
            // on error, roll back if you care
        });


        // remove from the “to-approve” stack after a second
        setTimeout(() => {
            setM(prev => prev.filter(item => item.id !== movie.id));
        }, 1000);
    }

    useEffect(() => {
        setMidIndex(Math.round((liked.length  / 2) - 1))
    }, [liked])

    useEffect(() => {
        const refreshParticipants = async () => {
            try {
                const res = await fetch(`/session/${code}/participants`);

                if (!res.ok) throw new Error('Network error');

                const parts = await res.json();
                
                if(!parts) return;

                const me = parts.find(p => p.session_id === sessionId);

                if (!me) {
                    console.warn(`No participant with session_id=${sessionId}`);
                    return [];
                }


                const tmpLiked = movies.filter(movie =>
                    me.approved.includes(movie.id)
                );

                
                if(liked.length === 0){
                    setLiked(tmpLiked)
                }

                setM((prev) => prev.filter((item) => !tmpLiked.includes(item)))

                setCommonLiked(parts[0].approved.filter(item =>
                    parts.every(p => p.approved.includes(item))
                ))
            } catch (e) {
                console.error('Failed to refresh participants:', e);
            }
        };

        refreshParticipants();
        setInterval(refreshParticipants, 1000);
    }, [code, currentName]);

    useEffect(() => {
        console.log(commonLiked)
    }, [commonLiked])

    function minMax(min, max, num){
        return min + max - num;
    }

    const clamp = (num, min, max) => Math.min(Math.max(num, min), max)

    return (
        <div className="flex items-center justify-between flex flex-col h-full w-full gap-[20px]">
            <div 
                className=' h-full w-max flex flex-col items-center overflow-hidden'
            >
                <div className='h-full'
                    
                />
                    <div className='flex flex-col h-full'>
                    
                    <div 
                        className='flex items-center justify-between  w-full h-[50px] gap-[25px]  z-1'
                    >
                        <button onClick={() => handleApproval(m[0], false)} className='w-[50px] aspect-[1/1] rounded-sm cursor-pointer'>
                            <TbTrashXFilled className='text-4xl text-red-500'/>
                        </button>
                        <div className='flex flex-col items-center justify-center w-[300px]'>
                            <AnimatePresence initial={false}>
                                {
                                    m
                                    .slice()
                                    .reverse()
                                    .map((movie, i) =>
                                        
                                        <motion.div 
                                            key={movie.id + "_" + i} 
                                            animate={likedIds.includes(movie.id) || dislikedIds.includes(movie.id) ? {top: 700, scale: 1.05} : {top: 0, scale: 1}}
                                            transition={{duration: 1, type: "spring", stiffness: 300, damping: 30 }}
                                            className='h-0 w-0 flex flex-col gap-[8px] items-center justify-center relative' 
                                            style={{zIndex: i+1}}
                                        >   
                                            {/* <div className='w-full h-0 flex justify-center'>
                                                <div className='min-w-[310px]  aspect-[1/1.5] h-[calc(310px_*_1.5)] flex items-center justify-center bg-white backdrop-blur-100 blur-sm opacity-20' style={{backgroundImage: `url(https://image.tmdb.org/t/p/w500${movie.poster_path})`}} />
                                            </div>
                                         */}
                                            <div className='min-w-[300px]  aspect-[1/1.5] rounded-md bg-white text-black bg-cover z-1' style={{backgroundImage: `url(https://image.tmdb.org/t/p/w500${movie.poster_path})`}}/>
                                        </motion.div>
                                    )
                                }
                                {
                                    m.length === 0
                                    &&
                                    <div className='w-[300px] aspect-[1/1.5] flex flex-col items-center justify-center gap-[10px]'>
                                        <h1 className='text-3xl'>
                                            Oh No!
                                        </h1>
                                        <p>
                                            It looks like you are all out of movies!
                                        </p>
                                    </div>
                                }
                            </AnimatePresence>
                        </div>
                        <button onClick={() => handleApproval(m[0], true)} className='w-[50px] aspect-[1/1] rounded-sm cursor-pointer'>
                            <FaCheckCircle className='text-4xl text-green-500'/>
                        </button>
                    </div>
                    <div 
                        className='flex items-end h-full min-w-full z-2'
                    >
                        <div 
                            className='h-[70px] w-full' 
                            style={{background: 'linear-gradient(0deg,rgba(3, 7, 18, 1) 20%, rgba(0, 0, 0, 0) 100%)'}}
                        >
                            
                        </div>
                    </div>
                </div>
            </div>
            <div className='flex w-max'>
                <div className='w-0 h-full flex z-1'>
                    <div className='min-w-[100px] h-full' style={{backgroundImage: "linear-gradient(90deg,rgba(3, 7, 18, 1) 20%, rgba(0, 0, 0, 0) 100%)"}}>
                    
                    </div>
                </div>
                <div className='flex h-[300px] w-[1000px] overflow-x-scroll overflow-y-hidden z-0 px-[100px]' style={{scrollbarWidth: "none"}}>

                    <LayoutGroup>
                        <motion.div className="relative flex justify-center items-center h-full w-max">
                            {liked.map((movie, idx) => {
                                const offset = idx - midIndex
                                
                                return (
                                    <motion.div
                                        key={movie.id}
                                        className=" w-[100px] flex flex-col perspective-normal"
                                        initial={{width: 0}}
                                        animate={{
                                            width:  100
                                        }}
                                    >   
                                        <motion.div
                                            initial={{ opacity: 0, y: 400}}
                                            animate={{
                                                opacity: 1,
                                                y: 0,
                                                x: offset * 10,          
                                                rotateY: 20,          
                                                scale: idx === Math.floor(midIndex) ? .68 : .72, 
                                            }}
                                            transition={{y: {delay: .5}, type: "spring", stiffness: 300, damping: 30 }}
                                        >
                                            <div className='max-w-0 max-h-0 bg-green-100 z-1 flex items-center justify-center'>
                                                {
                                                    commonLiked.includes(movie.id)
                                                    &&
                                                    <div className='min-w-[20px] min-h-[20px] bg-green-500 rounded-lg'/> 
                                                } 
                                            </div>
                                            <div
                                                className="w-[200px] h-[300px] bg-white rounded-md bg-cover shadow-lg z-0"
                                                style={{
                                                    backgroundImage: `url(https://image.tmdb.org/t/p/w500${movie.poster_path})`,
                                                }}
                                            />
                                        </motion.div>
                                    </motion.div>
                                )
                            })}
                        </motion.div>
                    </LayoutGroup>
                </div>
                <div className='w-0 h-full flex justify-end z-1'>
                    <div className='min-w-[100px] h-full' style={{backgroundImage: "linear-gradient(270deg,rgba(3, 7, 18, 1) 20%, rgba(0, 0, 0, 0) 100%)"}}>
                    
                    </div>
                </div>
            </div>
        </div>
    )
}
